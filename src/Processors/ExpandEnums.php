<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\GeneratorAwareInterface;
use OpenApi\GeneratorAwareTrait;
use OpenApi\OpenApiException;
use OpenApi\Undefined;
use OpenApi\Utils\ServerVariableEnum;

/**
 * Expands PHP enums.
 *
 * Determines <code>schema</code>, <code>enum</code> and <code>type</code>.
 */
class ExpandEnums implements GeneratorAwareInterface
{
    use GeneratorAwareTrait;

    protected ?string $enumNames;

    public function __construct(?string $enumNames = null)
    {
        $this->enumNames = $enumNames;
    }

    public function __invoke(Analysis $analysis): void
    {
        if (!class_exists('\\ReflectionEnum')) {
            return;
        }

        $this->expandContextEnum($analysis);
        $this->expandSchemaEnum($analysis);
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

    protected function expandContextEnum(Analysis $analysis): void
    {
        $schemas = $analysis->getAnnotationsOfType(OA\Schema::class, true);

        foreach ($schemas as $schema) {
            if ($schema->_context->is('enum')) {
                /** @var class-string<\UnitEnum> $enumName the 'enum' context key is the guarantee */
                $enumName = $schema->_context->fullyQualifiedName($schema->_context->enum) ?? '';
                $re = new \ReflectionEnum($enumName);
                $schema->schema = Undefined::isDefault($schema->schema) ? $re->getShortName() : $schema->schema;

                $schemaType = $schema->type;
                $enumType = null;
                if ($re->isBacked()) {
                    $enumType = $re->getBackingType()->getName();
                }

                // no (or invalid) schema type means name
                $useName = Undefined::isDefault($schemaType) || ($enumType && $this->generator->getTypeResolver()->native2spec($enumType) != $schemaType);

                $schema->enum = array_values(array_map(static fn (\ReflectionEnumUnitCase $case): int|string => ($useName || !($case instanceof \ReflectionEnumBackedCase)) ? $case->name : $case->getBackingValue(), $re->getCases()));

                if ($this->enumNames !== null && !$useName) {
                    $schemaX = Undefined::isDefault($schema->x) ? [] : $schema->x;
                    $schemaX[$this->enumNames] = array_map(static fn (\ReflectionEnumUnitCase $case): string => $case->name, $re->getCases());

                    $schema->x = $schemaX;
                }

                $schema->type = $useName ? 'string' : $enumType;

                $this->generator->getTypeResolver()->mapNativeType($schema, $schemaType);
            }
        }
    }

    protected function expandSchemaEnum(Analysis $analysis): void
    {
        $schemas = $analysis->getAnnotationsOfType([OA\Schema::class, OA\ServerVariable::class]);

        foreach ($schemas as $schema) {
            if (Undefined::isDefault($schema->enum)) {
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
                $enums[] = $enum instanceof \UnitEnum ? ($enum instanceof \BackedEnum ? $enum->value : $enum->name) : $enum;
            }

            // A Schema enum may hold any JSON value; a ServerVariable enum may not. Only the
            // latter narrows, which is why this sits here rather than in the loop above.
            if ($schema instanceof OA\ServerVariable) {
                $enums = ServerVariableEnum::asStrings($enums, $schema->_context->logger, Undefined::isDefault($schema->serverVariable) ? null : $schema->serverVariable);

                if ([] === $enums) {
                    // The spec says the array MUST NOT be empty, so an enum that normalised
                    // away is dropped rather than emitted empty. The spec compilers filter `[]`
                    // out on their own; this is classic catching up with them.
                    $schema->enum = Undefined::UNDEFINED;

                    continue;
                }
            }

            $schema->enum = $enums;
        }
    }
}
