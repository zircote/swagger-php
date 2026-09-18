<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\Config;
use OpenApi\Utils\PipeInterface;
use OpenApi\Utils\ServerVariableEnum;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Expands PHP enums into schema enum values.
 *
 * For schemas attached to a PHP enum, determines schema name, type, and enum values.
 * Also resolves UnitEnum instances and enum class-strings in any schema's or server
 * variable's enum array.
 *
 * Rules for name vs. value:
 * - Unit enums (not backed): always use case names, type becomes "string"
 * - Backed enums without explicit schema type: use case names, type becomes "string"
 * - Backed enums with schema type matching backing type (int→"integer", string→"string"):
 *   use backing values, type preserved
 * - Backed enums with schema type NOT matching backing type: use case names
 *
 * @implements PipeInterface<Specification>
 */
class Enums implements PipeInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        #[Config('If set, stores enum case names in a vendor extension with this key (e.g. <code>x-enum-varnames</code>).')]
        protected ?string $enumNames = null,
    ) {
    }

    public function __invoke(mixed $payload): mixed
    {
        $this->expandEnumSchemas($payload);
        $this->resolveEnumValues($payload);

        return null;
    }

    public function setEnumNames(?string $enumNames): static
    {
        $this->enumNames = $enumNames;

        return $this;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    protected function expandEnumSchemas(Specification $specification): void
    {
        foreach ($specification->schemas as $schema) {
            $reflector = $schema->getReflector();
            if (!$reflector instanceof \ReflectionClass || !$reflector->isEnum()) {
                continue;
            }

            /** @var class-string<\UnitEnum> $enumName isEnum() above is the guarantee */
            $enumName = $reflector->getName();
            $reflector = new \ReflectionEnum($enumName);

            $schema->schema ??= $reflector->getShortName();

            $useName = $this->shouldUseName($schema, $reflector);

            $schema->enum = array_values(array_map(
                static fn (\ReflectionEnumUnitCase $case): int|string => ($useName || !$case instanceof \ReflectionEnumBackedCase)
                    ? $case->name
                    : $case->getBackingValue(),
                $reflector->getCases(),
            ));

            if ($useName) {
                $schema->type = 'string';
            } else {
                $schema->type = $reflector->getBackingType()->getName() === 'int' ? 'integer' : 'string';
            }

            if ($this->enumNames !== null && !$useName && $reflector->isBacked()) {
                $names = array_map(
                    static fn (\ReflectionEnumUnitCase $case): string => $case->name,
                    $reflector->getCases(),
                );
                $schema->x = array_merge($schema->x ?? [], [$this->enumNames => $names]);
            }
        }
    }

    /**
     * @param \ReflectionEnum<\UnitEnum> $reflector
     */
    protected function shouldUseName(OA\Schema $schema, \ReflectionEnum $reflector): bool
    {
        if ($schema->type === null) {
            return true;
        }

        if (!$reflector->isBacked()) {
            return true;
        }

        $backingType = $reflector->getBackingType()->getName() === 'int' ? 'integer' : 'string';

        return $schema->type !== $backingType;
    }

    protected function resolveEnumValues(Specification $specification): void
    {
        $specification->getWalker()->visit(OA\Schema::class, function (OA\Schema $schema): void {
            if ($schema->enum !== null) {
                $schema->enum = $this->resolveEnumArray($schema->enum);
            }
        });

        $specification->getWalker()->visit(OA\ServerVariable::class, function (OA\ServerVariable $variable): void {
            if ($variable->enum !== null) {
                // resolveEnumArray() is shared with Schema, whose enum may hold any JSON value.
                // Only a server variable's is narrowed, and only after the cases are resolved.
                $variable->enum = ServerVariableEnum::asStrings(
                    $this->resolveEnumArray($variable->enum),
                    $this->logger,
                    $variable->serverVariable
                );
            }
        });
    }

    /**
     * @param list<mixed> $enum
     *
     * @return list<mixed>
     */
    protected function resolveEnumArray(array $enum): array
    {
        $resolved = [];
        foreach ($enum as $value) {
            if ($value instanceof \UnitEnum) {
                $resolved[] = $value instanceof \BackedEnum ? $value->value : $value->name;
            } elseif (is_string($value) && function_exists('enum_exists') && enum_exists($value)) {
                foreach ($value::cases() as $case) {
                    $resolved[] = $case instanceof \BackedEnum ? $case->value : $case->name;
                }
            } else {
                $resolved[] = $value;
            }
        }

        return $resolved;
    }
}
