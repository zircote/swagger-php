<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter\Inheritance;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\AttributeFactory;

/**
 * Expands PHP class hierarchy into OpenAPI composition (allOf).
 *
 * For each schema backed by a class reflector, walks parents, traits, and interfaces:
 * - Ancestor with #[Schema] → adds $ref to allOf, stops walking up (parents only)
 * - Ancestor without #[Schema] → merges its own members into the current schema
 */
class Schemas
{
    public function __construct(
        protected AttributeFactory $attributeFactory = new AttributeFactory(),
    ) {
    }

    public function resolve(Specification $payload): mixed
    {
        $schemaMap = $this->buildSchemaMap($payload);

        foreach ($payload->schemas as $schema) {
            $reflector = $schema->getClassReflector();
            if ($reflector === null) {
                continue;
            }

            $existingProperties = array_map(fn (OA\Property $property): ?string => $property->property, $schema->properties ?? []);

            $this->expandParents($schema, $reflector, $schemaMap, $existingProperties);
            $this->expandTraits($schema, $reflector, $schemaMap, $existingProperties);
            $this->expandInterfaces($schema, $reflector, $schemaMap, $existingProperties);
        }

        return null;
    }

    /**
     * @return array<string, OA\Schema> class name → schema
     */
    protected function buildSchemaMap(Specification $specification): array
    {
        $map = [];
        foreach ($specification->schemas as $schema) {
            $className = $schema->getClassName();
            if ($className !== null) {
                $map[$className] = $schema;
            }
        }

        return $map;
    }

    /**
     * @param array<string, OA\Schema> $schemaMap
     */
    protected function expandParents(OA\Schema $schema, \ReflectionClass $reflector, array $schemaMap, array &$existingProperties): void
    {
        // Walk up the inheritance chain; stop at the first ancestor that has its own schema
        // (it becomes a $ref). Non-schema ancestors have their members inlined.
        $parent = $reflector->getParentClass();
        while ($parent !== false) {
            if (isset($schemaMap[$parent->getName()])) {
                $this->addAllOfRef($schema, $schemaMap[$parent->getName()]);
                // stop on first schema ancestor
                break;
            }

            $this->mergeMembers($schema, $parent, $existingProperties);
            $parent = $parent->getParentClass();
        }
    }

    /**
     * @param array<string, OA\Schema> $schemaMap
     */
    protected function expandTraits(OA\Schema $schema, \ReflectionClass $reflector, array $schemaMap, array &$existingProperties): void
    {
        foreach ($this->attributeFactory->getDirectTraits($reflector) as $trait) {
            if (isset($schemaMap[$trait->getName()])) {
                $this->addAllOfRef($schema, $schemaMap[$trait->getName()]);
            } else {
                $this->mergeMembers($schema, $trait, $existingProperties);
            }
        }

        $parent = $reflector->getParentClass();
        while ($parent !== false) {
            if (isset($schemaMap[$parent->getName()])) {
                break;
            }

            foreach ($this->attributeFactory->getDirectTraits($parent) as $trait) {
                if (isset($schemaMap[$trait->getName()])) {
                    $this->addAllOfRef($schema, $schemaMap[$trait->getName()]);
                } else {
                    $this->mergeMembers($schema, $trait, $existingProperties);
                }
            }

            $parent = $parent->getParentClass();
        }
    }

    /**
     * @param array<string, OA\Schema> $schemaMap
     */
    protected function expandInterfaces(OA\Schema $schema, \ReflectionClass $reflector, array $schemaMap, array &$existingProperties): void
    {
        $ownInterfaces = $this->attributeFactory->getDirectInterfaces($reflector);

        foreach ($ownInterfaces as $interface) {
            if (isset($schemaMap[$interface->getName()])) {
                $this->addAllOfRef($schema, $schemaMap[$interface->getName()]);
            } else {
                $this->mergeMembers($schema, $interface, $existingProperties);
            }
        }
    }

    protected function addAllOfRef(OA\Schema $schema, OA\Schema $referenced): void
    {
        $schema->allOf ??= [];
        $name = $referenced->schema ?? $referenced->getShortClassName();
        if ($name !== null) {
            $schema->allOf[] = new OA\Schema(ref: '#/components/schemas/' . $name);
        }
    }

    protected function mergeMembers(OA\Schema $schema, \ReflectionClass $class, array &$existingProperties): void
    {
        $members = $this->attributeFactory->membersOf($class);
        $merged = [];
        foreach ($members as $member) {
            if ($member instanceof OA\Property) {
                // fallback to reflector if name not (yet) set
                $propertyName = $member->property ?? ($member->getReflector() instanceof \ReflectionProperty ? $member->getReflector()->name : null);
                if (!in_array($propertyName, $existingProperties, true)) {
                    $existingProperties[] = $propertyName;
                    $merged[] = $member;
                }
            }
        }

        if ($merged !== []) {
            $schema->properties = [...$merged, ...($schema->properties ?? [])];
        }
    }
}
