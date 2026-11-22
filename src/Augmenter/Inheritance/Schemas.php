<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter\Inheritance;

use OpenApi\ComponentNamer;
use OpenApi\Contracts\ComponentNamerInterface;
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
 *
 * A schema whose meta value under this class is `false` is left as it is: one a resolver built
 * from another description of the class, which already holds what the class inherits.
 */
class Schemas
{
    public function __construct(
        protected AttributeFactory $attributeFactory = new AttributeFactory(),
        protected ComponentNamerInterface $namer = new ComponentNamer\ShortName(),
    ) {
    }

    public function resolve(Specification $payload): mixed
    {
        $index = $payload->buildComponentIndex();

        foreach ($payload->schemas as $schema) {
            $reflector = $schema->getClassReflector();
            if ($reflector === null || $schema->getMeta(self::class) === false) {
                continue;
            }

            $existingProperties = array_map(static fn (OA\Property $property): ?string => $property->property, $schema->properties ?? []);

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
        // the class's own traits, then those of each parent up to the first with a schema
        $class = $reflector;
        do {
            foreach ($this->attributeFactory->getDirectTraits($class) as $trait) {
                $this->expandAncestor($schema, $trait, $index, $existingProperties, $merged);
            }
            $class = $class->getParentClass();
        } while ($class !== false && !$index->findSchema($class->getName()) instanceof OA\Schema);
    }

    /**
     * @param \ReflectionClass<object> $reflector
     * @param list<string|null>        $existingProperties
     * @param list<OA\Property>        $merged
     */
    protected function expandInterfaces(OA\Schema $schema, \ReflectionClass $reflector, ComponentIndex $index, array &$existingProperties, array &$merged): void
    {
        foreach ($this->attributeFactory->getDirectInterfaces($reflector) as $interface) {
            $this->expandAncestor($schema, $interface, $index, $existingProperties, $merged);
        }
    }

    /**
     * References an ancestor that has a schema, and merges the members of one that has none.
     *
     * @param \ReflectionClass<object> $ancestor
     * @param list<string|null>        $existingProperties
     * @param list<OA\Property>        $merged
     */
    protected function expandAncestor(OA\Schema $schema, \ReflectionClass $ancestor, ComponentIndex $index, array &$existingProperties, array &$merged): void
    {
        $ancestorSchema = $index->findSchema($ancestor->getName());
        if ($ancestorSchema instanceof OA\Schema) {
            $this->addAllOfRef($schema, $ancestorSchema);
        } else {
            $this->mergeMembers($ancestor, $existingProperties, $merged);
        }
    }

    protected function addAllOfRef(OA\Schema $schema, OA\Schema $referenced): void
    {
        $schema->allOf ??= [];
        // the explicit name or the namer's, as `Names` will name it — not `of()`, whose
        // title fallback would name a class schema after its title before `Names` has run
        $name = $referenced->component ?? $this->namer->name($referenced);
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
        foreach ($this->attributeFactory->membersOf($class) as $member) {
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
