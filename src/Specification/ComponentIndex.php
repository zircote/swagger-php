<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Specification;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\JsonPointer;

/**
 * Resolves `$ref` values to their corresponding component objects.
 *
 * Handles both canonical JSON Reference paths (`#/components/schemas/Foo`)
 * and FQCN refs (`App\Models\Foo`) that haven't been rewritten yet. A FQCN is looked up as a plain
 * `ComponentKey`, and finds nothing in a bucket where the class has more than one component.
 *
 * Indexes are built lazily per bucket on first access.
 */
class ComponentIndex
{
    protected const COMPONENTS_PREFIX = '#/components/';

    /** @var array<string, array<string, AttributeInterface>|null> */ protected array $indexes = [];

    /** @var array<string, array<string, list<AttributeInterface>>> bucket → key → components */ protected array $keyed = [];

    public function __construct(
        protected Specification $specification,
    ) {
    }

    public function find(string $ref): ?AttributeInterface
    {
        if (str_starts_with($ref, self::COMPONENTS_PREFIX)) {
            $path = substr($ref, strlen(self::COMPONENTS_PREFIX));
            $slash = strpos($path, '/');
            if ($slash === false) {
                return null;
            }

            $bucket = substr($path, 0, $slash);
            $name = JsonPointer::decode(substr($path, $slash + 1));

            return $this->getIndex($bucket)[$name] ?? null;
        }

        foreach (ComponentName::BUCKETS as $bucket) {
            $found = $this->getIndex($bucket)[$ref] ?? null;
            if ($found !== null) {
                return $found;
            }
        }

        return null;
    }

    public function findSchema(string $ref): ?OA\Schema
    {
        $result = str_starts_with($ref, self::COMPONENTS_PREFIX)
            ? $this->find($ref)
            : ($this->getIndex('schemas')[$ref] ?? null);

        return $result instanceof OA\Schema ? $result : null;
    }

    public function findResponse(string $ref): ?OA\Response
    {
        $result = str_starts_with($ref, self::COMPONENTS_PREFIX)
            ? $this->find($ref)
            : ($this->getIndex('responses')[$ref] ?? null);

        return $result instanceof OA\Response ? $result : null;
    }

    public function findRequestBody(string $ref): ?OA\RequestBody
    {
        $result = str_starts_with($ref, self::COMPONENTS_PREFIX)
            ? $this->find($ref)
            : ($this->getIndex('requestBodies')[$ref] ?? null);

        return $result instanceof OA\RequestBody ? $result : null;
    }

    public function findParameter(string $ref): ?OA\Parameter
    {
        $result = str_starts_with($ref, self::COMPONENTS_PREFIX)
            ? $this->find($ref)
            : ($this->getIndex('parameters')[$ref] ?? null);

        return $result instanceof OA\Parameter ? $result : null;
    }

    public function findHeader(string $ref): ?OA\Header
    {
        $result = str_starts_with($ref, self::COMPONENTS_PREFIX)
            ? $this->find($ref)
            : ($this->getIndex('headers')[$ref] ?? null);

        return $result instanceof OA\Header ? $result : null;
    }

    /**
     * The one component a key names in a bucket, or null when it names none or more than one.
     */
    public function findByKey(ComponentKey $key, string $bucket): ?AttributeInterface
    {
        $found = $this->findAllByKey($key, $bucket);

        return count($found) === 1 ? $found[0] : null;
    }

    /**
     * Every component a key names in a bucket, in the order they were contributed.
     *
     * @return list<AttributeInterface>
     */
    public function findAllByKey(ComponentKey $key, string $bucket): array
    {
        return $this->getKeyed($bucket)[(string) $key] ?? [];
    }

    /**
     * Whether a key names any component, in any bucket.
     */
    public function hasKey(ComponentKey $key): bool
    {
        foreach (ComponentName::BUCKETS as $bucket) {
            if ($this->findAllByKey($key, $bucket) !== []) {
                return true;
            }
        }

        return false;
    }

    /**
     * A parameter's identity, `in:name`, which is how OpenAPI tells two parameters apart. Null
     * when either half is unknown.
     *
     * A parameter that is only a `ref` takes both from the component it points to. One without
     * a `name` takes the name of the PHP parameter it was read from, the name
     * `Augmenter\Types` gives it later, so the answer is the same before and after that augmenter
     * runs.
     */
    public function parameterKey(OA\Parameter $parameter): ?string
    {
        $ref = $parameter->getRefValue();
        $target = is_string($ref) ? $this->findParameter($ref) : null;

        $reflector = $parameter->getReflector();
        $name = $parameter->name
            ?? $target->name
            ?? ($reflector instanceof \ReflectionParameter ? $reflector->getName() : null);
        $in = $parameter->in ?? $target?->in;

        return $name !== null && $in !== null ? $in . ':' . $name : null;
    }

    /**
     * A class whose plain key names exactly one named component in a bucket, mapped to it. Where a
     * class has a component in more than one bucket, the last bucket wins.
     *
     * @deprecated since 7.1, removed in 8.0; use `findByKey()`, which says which bucket a use means
     *
     * @return array<string, string> FQCN → #/components/{type}/{name}
     */
    public function buildRefMap(): array
    {
        $map = [];

        foreach (ComponentName::BUCKETS as $bucket) {
            foreach ($this->getKeyed($bucket) as $components) {
                $key = $components[0]->getComponentKey();
                $name = ComponentName::of($components[0]);
                if (count($components) === 1 && $key?->isPlain() === true && $name !== null) {
                    $map[$key->class] = JsonPointer::ref('components', $bucket, $name);
                }
            }
        }

        return $map;
    }

    /**
     * @return array<string, AttributeInterface>
     */
    protected function getIndex(string $bucket): array
    {
        if (!in_array($bucket, ComponentName::BUCKETS, true)) {
            return [];
        }

        if (!array_key_exists($bucket, $this->indexes)) {
            $this->indexes[$bucket] = $this->buildIndex($bucket);
        }

        return $this->indexes[$bucket];
    }

    /**
     * @return array<string, AttributeInterface>
     */
    protected function buildIndex(string $bucket): array
    {
        $index = [];

        foreach ($this->specification->{$bucket} as $item) {
            $name = ComponentName::of($item);
            if ($name !== null) {
                $index[$name] = $item;
            }
        }

        foreach ($this->getKeyed($bucket) as $components) {
            $key = $components[0]->getComponentKey();
            if (count($components) === 1 && $key?->isPlain() === true) {
                $index[$key->class] = $components[0];
            }
        }

        return $index;
    }

    /**
     * @return array<string, list<AttributeInterface>>
     */
    protected function getKeyed(string $bucket): array
    {
        if (!in_array($bucket, ComponentName::BUCKETS, true)) {
            return [];
        }

        if (!array_key_exists($bucket, $this->keyed)) {
            $this->keyed[$bucket] = [];
            foreach ($this->specification->{$bucket} as $item) {
                $key = $item->getComponentKey();
                if ($key instanceof ComponentKey) {
                    $this->keyed[$bucket][(string) $key][] = $item;
                }
            }
        }

        return $this->keyed[$bucket];
    }
}
