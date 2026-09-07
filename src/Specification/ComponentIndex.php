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
 * and FQCN refs (`App\Models\Foo`) that haven't been rewritten yet.
 *
 * Indexes are built lazily per bucket on first access.
 */
class ComponentIndex
{
    protected const COMPONENTS_PREFIX = '#/components/';

    /** @var array<string, array<string, AttributeInterface>|null> */ protected array $indexes = [];

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
     * @return array<string, string> FQCN → #/components/{type}/{name}
     */
    public function buildRefMap(): array
    {
        $map = [];

        foreach (ComponentName::BUCKETS as $bucket) {
            $items = $this->specification->{$bucket};

            foreach ($items as $item) {
                $name = ComponentName::of($item);
                $fqcn = $item->getClassName();
                if ($name !== null && $fqcn !== null) {
                    $map[$fqcn] = JsonPointer::ref('components', $bucket, $name);
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
        $items = $this->specification->{$bucket};
        $index = [];

        foreach ($items as $item) {
            $name = ComponentName::of($item);
            if ($name !== null) {
                $index[$name] = $item;
            }

            $fqcn = $item->getClassName();
            if ($fqcn !== null) {
                $index[$fqcn] = $item;
            }
        }

        return $index;
    }
}
