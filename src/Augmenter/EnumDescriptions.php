<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\PipeInterface;

/**
 * Generates a description for enum-based properties.
 *
 * @implements PipeInterface<Specification>
 */
class EnumDescriptions implements PipeInterface
{
    public function __construct(protected bool $enabled = false)
    {
    }

    /**
     * Enables/disables generation of descriptions for enum based properties.
     */
    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Augment;
    }

    public function __invoke(mixed $payload): null
    {
        if (!$this->enabled) {
            return null;
        }

        $this->expandEnumDescriptions($payload);

        return null;
    }

    protected function expandEnumDescriptions(Specification $specification): void
    {
        $specification->getWalker()->eachSchema(function (OA\Schema $schema): void {
            if ($schema->properties === null) {
                return;
            }

            foreach ($schema->properties as $property) {
                $reflector = $property->getReflector();
                if (!$reflector instanceof \ReflectionProperty) {
                    continue;
                }

                $type = $reflector->getType();
                if (!$type instanceof \ReflectionNamedType || $type->isBuiltin()) {
                    continue;
                }

                $typeName = $type->getName();
                if (!enum_exists($typeName)) {
                    continue;
                }

                $this->expandEnumDescriptionsForProperty($property, new \ReflectionEnum($typeName));
            }
        });
    }

    protected function expandEnumDescriptionsForProperty(OA\Property $property, \ReflectionEnum $reflector): void
    {
        $values = [];
        if ($reflector->isBacked()) {
            foreach ($reflector->getCases() as $case) {
                $values[] = $case->getBackingValue() . ':' . $case->getName();
            }
        } else {
            foreach ($reflector->getCases() as $case) {
                $values[] = $case->getName();
            }
        }

        $property->schema ??= new OA\Schema();
        if ($property->schema->description === null) {
            $property->schema->description = $reflector->getShortName() . ' (' . implode('; ', $values) . ')';
        }
    }
}
