<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\OpenApiException;

/**
 * Expands PHP enums.
 *
 * Determines <code>schema</code>, <code>enum</code> and <code>type</code>.
 */
class ExpandEnums
{
    use Concerns\TypesTrait;

    protected ?string $enumNames;

    public function __construct(?string $enumNames = null)
    {
        $this->enumNames = $enumNames;
    }

    public function getEnumNames(): ?string
    {
        return $this->enumNames;
    }

    /**
     * Specifies the name of the extension variable where backed enum names will be stored.
     * Set to <code>null</code> to avoid writing backed enum names.
     *
     * Example:
     * <code>->setEnumNames('enumNames')</code> yields:
     * ```yaml
     *   x-enumNames:
     *     - NAME1
     *     - NAME2
     * ```
     */
    public function setEnumNames(?string $enumNames = null): void
    {
        $this->enumNames = $enumNames;
    }

    public function __invoke(Analysis $analysis)
    {
        if (!class_exists('\\ReflectionEnum')) {
            return;
        }

        $this->expandContextEnum($analysis);
        $this->expandSchemaEnum($analysis);
    }

    protected function expandContextEnum(Analysis $analysis): void
    {
        /** @var OA\Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(OA\Schema::class, true);

        foreach ($schemas as $schema) {
            if ($schema->_context->is('enum')) {
                $re = new \ReflectionEnum($schema->_context->fullyQualifiedName($schema->_context->enum));
                $schema->schema = Generator::isDefault($schema->schema) ? $re->getShortName() : $schema->schema;

                $schemaType = $schema->type;
                $enumType = null;
                if ($re->isBacked()) {
                    $backingType = $re->getBackingType();
                    if ($backingType instanceof \ReflectionNamedType) {
                        $enumType = $backingType->getName();
                    }
                }

                // no (or invalid) schema type means name
                $useName = Generator::isDefault($schemaType) || ($enumType && $this->native2spec($enumType) != $schemaType);

                $schema->enum = array_map(function ($case) use ($useName) {
                    return ($useName || !($case instanceof \ReflectionEnumBackedCase)) ? $case->name : $case->getBackingValue();
                }, $re->getCases());

                if ($this->enumNames !== null && !$useName) {
                    $schemaX = Generator::isDefault($schema->x) ? [] : $schema->x;
                    $schemaX[$this->enumNames] = array_map(function ($case) {
                        return $case->name;
                    }, $re->getCases());

                    $schema->x = $schemaX;
                }

                $schema->type = $useName ? 'string' : $enumType;

                $this->mapNativeType($schema, $schemaType);
            }
        }
    }

    protected function expandSchemaEnum(Analysis $analysis): void
    {
        /** @var OA\Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType([OA\Schema::class, OA\ServerVariable::class]);

        foreach ($schemas as $schema) {
            if (Generator::isDefault($schema->enum)) {
                continue;
            }

            if (is_string($schema->enum)) {
                // might be enum class-string
                if (is_a($schema->enum, \UnitEnum::class, true)) {
                    $cases = $schema->enum::cases();
                } else {
                    throw new OpenApiException("Unexpected enum value, requires specifying the Enum class string: $schema->enum");
                }
            } else {
                // might be an array of \UnitEnum::class, string, int, etc...
                assert(is_array($schema->enum));

                $cases = [];

                // transform \UnitEnum into individual cases
                /** @var string|class-string<\UnitEnum> $enum */
                foreach ($schema->enum as $enum) {
                    if (is_string($enum) && function_exists('enum_exists') && enum_exists($enum)) {
                        foreach ($enum::cases() as $case) {
                            $cases[] = $case;
                        }
                    } else {
                        $cases[] = $enum;
                    }
                }
            }

            $enums = [];
            foreach ($cases as $enum) {
                $enums[] = is_a($enum, \UnitEnum::class) ? $enum->value ?? $enum->name : $enum;
            }

            $schema->enum = $enums;
        }
    }
}
