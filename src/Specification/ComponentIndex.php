<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Specification;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;

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

    protected const BUCKET_MAP = [
        'schemas' => 'schemas',
        'responses' => 'responses',
        'parameters' => 'parameters',
        'requestBodies' => 'requestBodies',
        'headers' => 'headers',
        'securitySchemes' => 'securitySchemes',
        'links' => 'links',
        'examples' => 'examples',
    ];

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
            $name = substr($path, $slash + 1);

            return $this->getIndex($bucket)[$name] ?? null;
        }

        foreach (array_keys(self::BUCKET_MAP) as $bucket) {
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

        foreach (self::BUCKET_MAP as $bucket => $property) {
            $items = $this->specification->{$property};

            foreach ($items as $item) {
                $name = $this->getComponentName($item, $bucket);
                $fqcn = $item->getClassName();
                if ($name !== null && $fqcn !== null) {
                    $map[$fqcn] = self::COMPONENTS_PREFIX . $bucket . '/' . $name;
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
        if (!isset(self::BUCKET_MAP[$bucket])) {
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
        $property = self::BUCKET_MAP[$bucket];
        $items = $this->specification->{$property};
        $index = [];

        foreach ($items as $item) {
            $name = $this->getComponentName($item, $bucket);
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

    protected function getComponentName(AttributeInterface $item, string $bucket): ?string
    {
        return match ($bucket) {
            'schemas' => $item instanceof OA\Schema ? ($item->schema ?? $item->title) : null,
            'responses' => $item instanceof OA\Response ? ($item->response !== null ? (string) $item->response : null) : null,
            'requestBodies' => $item instanceof OA\RequestBody ? $item->request : null,
            'headers' => $item instanceof OA\Header ? $item->header : null,
            'parameters' => $item instanceof OA\Parameter ? ($item->parameter ?? $item->name) : null,
            'securitySchemes' => $item instanceof OA\Security\Scheme ? $item->securityScheme : null,
            'links' => $item instanceof OA\Link ? $item->link : null,
            'examples' => $item instanceof OA\Example ? $item->example : null,
            default => null,
        };
    }
}
