<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter\Inheritance;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Specification\ComponentIndex;
use OpenApi\Utils\AttributeFactory;
use OpenApi\Utils\JsonPointer;

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
        $index = $payload->buildComponentIndex();

        foreach ($payload->schemas as $schema) {
            $reflector = $schema->getClassReflector();
            if ($reflector === null) {
                continue;
            }

            $existingProperties = array_map(fn (OA\Property $property): ?string => $property->property, $schema->properties ?? []);

            // merged members accumulate in visit order and are prepended once; prepending per
            // source would emit each group of siblings - traits, most of all - in reverse
            $merged = [];

            $this->expandParents($schema, $reflector, $index, $existingProperties, $merged);
            $this->expandTraits($schema, $reflector, $index, $existingProperties, $merged);
            $this->expandInterfaces($schema, $reflector, $index, $existingProperties, $merged);

            if ($merged !== []) {
                $schema->properties = [...$merged, ...($schema->properties ?? [])];
            }
        }

        return null;
    }

    /**
     * @param \ReflectionClass<object> $reflector
     * @param list<string|null>        $existingProperties
     * @param list<OA\Property>        $merged
     */
    protected function expandParents(OA\Schema $schema, \ReflectionClass $reflector, ComponentIndex $index, array &$existingProperties, array &$merged): void
    {
        $parent = $reflector->getParentClass();
        while ($parent !== false) {
            $parentSchema = $index->findSchema($parent->getName());
            if ($parentSchema instanceof OA\Schema) {
                $this->addAllOfRef($schema, $parentSchema);
                break;
            }

            $this->mergeMembers($parent, $existingProperties, $merged);
            $parent = $parent->getParentClass();
        }
    }

    /**
     * @param \ReflectionClass<object> $reflector
     * @param list<string|null>        $existingProperties
     * @param list<OA\Property>        $merged
     */
    protected function expandTraits(OA\Schema $schema, \ReflectionClass $reflector, ComponentIndex $index, array &$existingProperties, array &$merged): void
    {
        foreach ($this->attributeFactory->getDirectTraits($reflector) as $trait) {
            $traitSchema = $index->findSchema($trait->getName());
            if ($traitSchema instanceof OA\Schema) {
                $this->addAllOfRef($schema, $traitSchema);
            } else {
                $this->mergeMembers($trait, $existingProperties, $merged);
            }
        }

        $parent = $reflector->getParentClass();
        while ($parent !== false) {
            if ($index->findSchema($parent->getName()) instanceof OA\Schema) {
                break;
            }

            foreach ($this->attributeFactory->getDirectTraits($parent) as $trait) {
                $traitSchema = $index->findSchema($trait->getName());
                if ($traitSchema instanceof OA\Schema) {
                    $this->addAllOfRef($schema, $traitSchema);
                } else {
                    $this->mergeMembers($trait, $existingProperties, $merged);
                }
            }

            $parent = $parent->getParentClass();
        }
    }

    /**
     * @param \ReflectionClass<object> $reflector
     * @param list<string|null>        $existingProperties
     * @param list<OA\Property>        $merged
     */
    protected function expandInterfaces(OA\Schema $schema, \ReflectionClass $reflector, ComponentIndex $index, array &$existingProperties, array &$merged): void
    {
        $ownInterfaces = $this->attributeFactory->getDirectInterfaces($reflector);

        foreach ($ownInterfaces as $interface) {
            $interfaceSchema = $index->findSchema($interface->getName());
            if ($interfaceSchema instanceof OA\Schema) {
                $this->addAllOfRef($schema, $interfaceSchema);
            } else {
                $this->mergeMembers($interface, $existingProperties, $merged);
            }
        }
    }

    protected function addAllOfRef(OA\Schema $schema, OA\Schema $referenced): void
    {
        $schema->allOf ??= [];
        $name = $referenced->schema ?? $referenced->getShortClassName();
        if ($name !== null) {
            $schema->allOf[] = new OA\Schema(ref: JsonPointer::ref('components', 'schemas', $name));
        }
    }

    /**
     * @param \ReflectionClass<object> $class
     * @param list<string|null>        $existingProperties
     * @param list<OA\Property>        $merged
     */
    protected function mergeMembers(\ReflectionClass $class, array &$existingProperties, array &$merged): void
    {
        $members = $this->attributeFactory->membersOf($class);
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
    }
}
