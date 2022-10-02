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
 * Look at all enums with a schema and:
 * - set the name `schema`
 * - set `enum` values.
 */
class ExpandEnums
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

    private function expandContextEnum(Analysis $analysis): void
    {
        /** @var OA\Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType([OA\Schema::class, OAT\Schema::class], true);

        foreach ($schemas as $schema) {
            if ($schema->_context->is('enum')) {
                $source = $schema->_context->enum;
                $re = new \ReflectionEnum($schema->_context->fullyQualifiedName($source));
                $schema->schema = !Generator::isDefault($schema->schema) ? $schema->schema : $re->getShortName();
                $type = 'string';
                $schemaType = 'string';
                if ($re->isBacked() && ($backingType = $re->getBackingType()) && method_exists($backingType, 'getName')) {
                    if (Generator::isDefault($schema->type)) {
                        $type = $backingType->getName();
                    } else {
                        $type = $schema->type;
                        $schemaType = $schema->type;
                    }
                }
                $schema->enum = array_map(function ($case) use ($re, $schemaType, $type) {
                    if ($re->isBacked() && $type === $schemaType) {
                        return $case->getBackingValue();
                    }

                    return $case->name;
                }, $re->getCases());
                $this->mapNativeType($schema, $type);
            }
        }
    }

    private function expandSchemaEnum(Analysis $analysis): void
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
                // might be enum class
                if (is_a($schema->enum, \UnitEnum::class, true)) {
                    $source = $schema->enum::cases();
                } else {
                    throw new \InvalidArgumentException("Unexpected enum value, requires specifying the Enum class string: $schema->enum");
                }
            } elseif (is_array($schema->enum)) {
                // might be array of enum, string, int, etc...
                $source = $schema->enum;
            } else {
                throw new \InvalidArgumentException('Unexpected enum value, requires Enum class string or array');
            }

            $enums = [];
            foreach ($source as $enum) {
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
