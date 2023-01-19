<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;
use OpenApi\Generator;

/**
 * Expands PHP enums.
 *
 * Determines `schema`, `enum` and `type`.
 */
class ExpandEnums implements ProcessorInterface
{
    use Concerns\TypesTrait;

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
        $schemas = $analysis->getAnnotationsOfType([OA\Schema::class, OAT\Schema::class], true);

        foreach ($schemas as $schema) {
            if ($schema->_context->is('enum')) {
                $re = new \ReflectionEnum($schema->_context->fullyQualifiedName($schema->_context->enum));
                $schema->schema = !Generator::isDefault($schema->schema) ? $schema->schema : $re->getShortName();

                $schemaType = $schema->type;
                $enumType = null;
                if ($re->isBacked() && ($backingType = $re->getBackingType()) && $backingType instanceof \ReflectionNamedType) {
                    $enumType = $backingType->getName();
                }

                // no (or invalid) schema type means name
                $useName = Generator::isDefault($schemaType) || ($enumType && $this->native2spec($enumType) != $schemaType);

                $schema->enum = array_map(function ($case) use ($useName) {
                    return $useName ? $case->name : $case->getBackingValue();
                }, $re->getCases());

                $schema->type = $useName ? 'string' : $enumType;

                $this->mapNativeType($schema, $schemaType);
            }
        }
    }

    protected function expandSchemaEnum(Analysis $analysis): void
    {
        /** @var OA\Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType([
            OA\Schema::class,
            OAT\Schema::class,
            OA\ServerVariable::class,
            OAT\ServerVariable::class,
        ]);

        foreach ($schemas as $schema) {
            if (Generator::isDefault($schema->enum)) {
                continue;
            }

            if (is_string($schema->enum)) {
                // might be enum class-string
                if (is_a($schema->enum, \UnitEnum::class, true)) {
                    $cases = $schema->enum::cases();
                } else {
                    throw new \InvalidArgumentException("Unexpected enum value, requires specifying the Enum class string: $schema->enum");
                }
            } elseif (is_array($schema->enum)) {
                // might be an array of \UnitEnum::class, string, int, etc...
                $cases = $schema->enum;
            } else {
                throw new \InvalidArgumentException('Unexpected enum value, requires Enum class string or array');
            }

            $enums = [];
            foreach ($cases as $enum) {
                if (is_a($enum, \UnitEnum::class)) {
                    $enums[] = $enum->value ?? $enum->name;
                } else {
                    $enums[] = $enum;
                }
            }

            $schema->enum = $enums;
        }
    }
}
