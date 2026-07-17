<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\AttributeFactory;
use OpenApi\Utils\PipeInterface;

/**
 * Expands PHP class hierarchy into OpenAPI composition (allOf).
 *
 * For each schema backed by a class reflector, walks parents, traits, and interfaces:
 * - Ancestor with #[Schema] → adds $ref to allOf, stops walking up (parents only)
 * - Ancestor without #[Schema] → merges its own members into the current schema
 *
 * After expansion, if a schema has both allOf and properties, the properties are
 * moved into a dedicated allOf entry (anonymous schema with type: object).
 *
 * @implements PipeInterface<Specification>
 */
class Inheritance implements PipeInterface
{
    public function __construct(
        protected AttributeFactory $factory = new AttributeFactory(),
    ) {
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    public function __invoke(mixed $payload): mixed
    {
        $schemaMap = $this->buildSchemaMap($payload);

        foreach ($payload->schemas as $schema) {
            $reflector = $schema->getClassReflector();
            if ($reflector === null) {
                continue;
            }

            $this->expandParents($schema, $reflector, $schemaMap);
            $this->expandTraits($schema, $reflector, $schemaMap);
            $this->expandInterfaces($schema, $reflector, $schemaMap);
            $this->mergeAllOf($schema);
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
    protected function expandParents(OA\Schema $schema, \ReflectionClass $reflector, array $schemaMap): void
    {
        // Walk up the inheritance chain; stop at the first ancestor that has its own schema
        // (it becomes a $ref). Non-schema ancestors have their members inlined.
        $parent = $reflector->getParentClass();
        while ($parent !== false) {
            if (isset($schemaMap[$parent->getName()])) {
                $this->addAllOfRef($schema, $schemaMap[$parent->getName()]);
                break;
            }

            $this->mergeMembers($schema, $parent);
            $parent = $parent->getParentClass();
        }
    }

    /**
     * @param array<string, OA\Schema> $schemaMap
     */
    protected function expandTraits(OA\Schema $schema, \ReflectionClass $reflector, array $schemaMap): void
    {
        // PHP reports trait properties as declared by the using class, so the
        // assembler already collected them as own properties. For traits with a
        // schema we add an allOf $ref and strip those properties; for traits
        // without a schema we leave them in place (already present, no merge needed).
        foreach ($this->getDirectTraits($reflector) as $trait) {
            if (isset($schemaMap[$trait->getName()])) {
                $this->addAllOfRef($schema, $schemaMap[$trait->getName()]);
                $this->stripTraitProperties($schema, $trait);
            }
        }

        // Traits from non-schema ancestors also belong to this schema (the ancestor
        // itself was inlined in expandParents, but its traits weren't covered there).
        $parent = $reflector->getParentClass();
        while ($parent !== false) {
            if (isset($schemaMap[$parent->getName()])) {
                break;
            }

            foreach ($this->getDirectTraits($parent) as $trait) {
                if (isset($schemaMap[$trait->getName()])) {
                    $this->addAllOfRef($schema, $schemaMap[$trait->getName()]);
                    $this->stripTraitProperties($schema, $trait);
                }
            }

            $parent = $parent->getParentClass();
        }
    }

    /**
     * @param array<string, OA\Schema> $schemaMap
     */
    protected function expandInterfaces(OA\Schema $schema, \ReflectionClass $reflector, array $schemaMap): void
    {
        $ownInterfaces = $this->getDirectInterfaces($reflector);

        foreach ($ownInterfaces as $interface) {
            if (isset($schemaMap[$interface->getName()])) {
                $this->addAllOfRef($schema, $schemaMap[$interface->getName()]);
            } else {
                $this->mergeMembers($schema, $interface);
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

    protected function mergeMembers(OA\Schema $schema, \ReflectionClass $class): void
    {
        $members = $this->factory->membersOf($class);
        $merged = [];
        foreach ($members as $member) {
            if ($member instanceof OA\Property) {
                $merged[] = $member;
            }
        }

        if ($merged !== []) {
            $schema->properties = [...$merged, ...($schema->properties ?? [])];
        }
    }

    protected function stripTraitProperties(OA\Schema $schema, \ReflectionClass $trait): void
    {
        if ($schema->properties === null) {
            return;
        }

        $traitPropertyNames = array_map(
            fn (\ReflectionProperty $property): string => $property->getName(),
            $trait->getProperties(),
        );

        $schema->properties = array_values(array_filter(
            $schema->properties,
            fn (OA\Property $property): bool => !in_array($property->property, $traitPropertyNames, true),
        ));

        if ($schema->properties === []) {
            $schema->properties = null;
        }
    }

    /**
     * When allOf refs were added, but the schema also has own properties, wrap them
     * in a dedicated allOf entry so the final output is a pure allOf composition.
     *
     * Own properties are always first.
     */
    protected function mergeAllOf(OA\Schema $schema): void
    {
        if ($schema->allOf === null || $schema->properties === null) {
            return;
        }

        array_unshift($schema->allOf,new OA\Schema(type: 'object', properties: $schema->properties));
        $schema->properties = null;
    }

    /**
     * Get traits directly used by a class (not inherited via parents).
     *
     * @return list<\ReflectionClass>
     */
    protected function getDirectTraits(\ReflectionClass $class): array
    {
        $traits = $class->getTraits();
        // Exclude traits inherited from parent
        $parent = $class->getParentClass();
        if ($parent !== false) {
            $parentTraitNames = array_map(
                fn (\ReflectionClass $trait): string => $trait->getName(),
                $parent->getTraits(),
            );
            $traits = array_filter(
                $traits,
                fn (\ReflectionClass $trait): bool => !in_array($trait->getName(), $parentTraitNames, true),
            );
        }

        return array_values($traits);
    }

    /**
     * Get interfaces directly implemented by a class (not inherited from parents).
     *
     * @return list<\ReflectionClass>
     */
    protected function getDirectInterfaces(\ReflectionClass $class): array
    {
        $interfaces = $class->getInterfaces();

        $parent = $class->getParentClass();
        if ($parent !== false) {
            $parentInterfaceNames = array_map(
                fn (\ReflectionClass $interface): string => $interface->getName(),
                $parent->getInterfaces(),
            );
            $interfaces = array_filter(
                $interfaces,
                fn (\ReflectionClass $interface): bool => !in_array($interface->getName(), $parentInterfaceNames, true),
            );
        }

        return array_values($interfaces);
    }
}
