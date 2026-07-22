<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\AttributeFactory;
use OpenApi\Utils\PipeInterface;
use OpenApi\Utils\TokenScanner;

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
        protected AttributeFactory $attributeFactory = new AttributeFactory(),
        protected TokenScanner $tokenScanner = new TokenScanner(),
    ) {
    }

    public function setAttributeFactory(AttributeFactory $attributeFactory): void
    {
        $this->attributeFactory = $attributeFactory;
    }

    public function setTokenScanner(TokenScanner $tokenScanner): void
    {
        $this->tokenScanner = $tokenScanner;
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

            $existingProperties = array_map(fn (OA\Property $p): ?string => $p->property, $schema->properties ?? []);
            $this->expandParents($schema, $reflector, $schemaMap, $existingProperties);
            $this->expandTraits($schema, $reflector, $schemaMap, $existingProperties);
            $this->expandInterfaces($schema, $reflector, $schemaMap, $existingProperties);
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
    protected function expandParents(OA\Schema $schema, \ReflectionClass $reflector, array $schemaMap, array &$existingProperties): void
    {
        // Walk up the inheritance chain; stop at the first ancestor that has its own schema
        // (it becomes a $ref). Non-schema ancestors have their members inlined.
        $parent = $reflector->getParentClass();
        while ($parent !== false) {
            if (isset($schemaMap[$parent->getName()])) {
                $this->addAllOfRef($schema, $schemaMap[$parent->getName()]);
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
        foreach ($this->getDirectTraits($reflector) as $trait) {
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

            foreach ($this->getDirectTraits($parent) as $trait) {
                if (isset($schemaMap[$trait->getName()])) {
                    $this->addAllOfRef($schema, $schemaMap[$trait->getName()]);
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
        $ownInterfaces = $this->getDirectInterfaces($reflector);

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
            if ($member instanceof OA\Property && !in_array($member->property, $existingProperties, true)) {
                $existingProperties[] = $member->property;
                $merged[] = $member;
            }
        }

        if ($merged !== []) {
            $schema->properties = [...$merged, ...($schema->properties ?? [])];
        }
    }

    /**
     * When allOf refs were added but the schema also has own properties, wrap them
     * in a dedicated allOf entry so the final output is a pure allOf composition.
     */
    protected function mergeAllOf(OA\Schema $schema): void
    {
        if ($schema->allOf === null || $schema->properties === null) {
            return;
        }

        $schema->allOf[] = new OA\Schema(type: 'object', properties: $schema->properties);
        $schema->properties = null;
    }

    /**
     * Get traits directly used by a class (not inherited via parents or other traits).
     *
     * PHP's ReflectionClass::getTraits() flattens the entire trait tree, so we
     * must exclude traits that come from a parent class or from another trait's use.
     *
     * @return list<\ReflectionClass>
     */
    protected function getDirectTraits(\ReflectionClass $class): array
    {
        $scannerDetails = $this->tokenScanner->detailsFor($class);

        if ($scannerDetails !== null) {
            return array_filter(
                array_map(
                    fn (string $name): ?\ReflectionClass => class_exists($name) || trait_exists($name) ? new \ReflectionClass($name) : null,
                    $scannerDetails['traits'],
                ),
            );
        }

        return [];
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
                fn (\ReflectionClass $i): string => $i->getName(),
                $parent->getInterfaces(),
            );
            $interfaces = array_filter(
                $interfaces,
                fn (\ReflectionClass $i): bool => !in_array($i->getName(), $parentInterfaceNames, true),
            );
        }

        return array_values($interfaces);
    }
}
