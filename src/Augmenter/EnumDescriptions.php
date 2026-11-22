<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Undefined;
use OpenApi\Utils\Config;
use OpenApi\Utils\PipeInterface;

/**
 * Generates a description for enum-based properties.
 *
 * @implements PipeInterface<Specification>
 */
class EnumDescriptions implements PipeInterface
{
    public function __construct(
        #[Config('Enables/disables generation of descriptions for enum based properties.')]
        protected bool $enabled = false,
    ) {
    }

    public function __invoke(mixed $payload): mixed
    {
        if (!$this->enabled) {
            return null;
        }

        $payload->getWalker()->eachSchema(function (OA\Schema $schema): void {
            foreach ($schema->properties ?? [] as $property) {
                $reflector = $property->getReflector();
                $type = $reflector instanceof \ReflectionProperty ? $reflector->getType() : null;
                if ($type instanceof \ReflectionNamedType && !$type->isBuiltin() && enum_exists($type->getName())) {
                    $this->expandEnumDescriptionsForProperty($property, new \ReflectionEnum($type->getName()));
                }
            }
        });

        return null;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Augment;
    }

    /**
     * @param \ReflectionEnum<\UnitEnum> $reflector
     */
    protected function expandEnumDescriptionsForProperty(OA\Property $property, \ReflectionEnum $reflector): void
    {
        $values = array_map(
            static fn (\ReflectionEnumUnitCase $case): string => $case instanceof \ReflectionEnumBackedCase
                ? $case->getBackingValue() . ':' . $case->getName()
                : $case->getName(),
            $reflector->getCases(),
        );

        $property->schema ??= new OA\Schema();
        if (Undefined::isDefault($property->schema->description)) {
            $property->schema->description = $reflector->getShortName() . ' (' . implode('; ', $values) . ')';
        }
    }
}
