<?php
declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations\Items as AnnotationsItems;
use OpenApi\Annotations\JsonContent as AnnotationsJsonContent;
use OpenApi\Annotations\Property as AnnotationsProperty;
use OpenApi\Annotations\Schema as AnnotationSchema;
use OpenApi\Annotations\XmlContent as AnnotationsXmlContent;
use OpenApi\Attributes\AdditionalProperties as AttributesAdditionalProperties;
use OpenApi\Annotations\AdditionalProperties as AnnotationsAdditionalProperties;
use OpenApi\Attributes\Items as AttributesItems;
use OpenApi\Attributes\JsonContent as AttributesJsonContent;
use OpenApi\Attributes\Property as AttributesProperty;
use OpenApi\Attributes\Schema as AttributeSchema;
use OpenApi\Attributes\ServerVariable as AttributesServerVariable;
use OpenApi\Annotations\ServerVariable as AnnotationsServerVariable;
use OpenApi\Attributes\XmlContent as AttributesXmlContent;
use OpenApi\Generator;

/**
 * Look at all enums with a schema and:
 * - set the name `schema`
 * - set `enum` values.
 */
class ExpandEnums
{
    use Concerns\TypesTrait;

    /**
     * @param Analysis $analysis
     *
     * @throws \ReflectionException
     */
    public function __invoke(Analysis $analysis)
    {
        if (!class_exists('\\ReflectionEnum')) {
            return;
        }

        $this->expandContextEnum($analysis);
        $this->expandSchemaEnum($analysis);
    }

    /**
     * @param Analysis $analysis
     *
     * @throws \ReflectionException
     */
    private function expandContextEnum(Analysis $analysis): void
    {
        /** @var AnnotationSchema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType([AnnotationSchema::class, AttributeSchema::class], true);

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

    /**
     * @param Analysis $analysis
     */
    private function expandSchemaEnum(Analysis $analysis): void
    {
        /** @var AnnotationSchema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType([
            AnnotationsAdditionalProperties::class,
            AttributesAdditionalProperties::class,
            AnnotationsItems::class,
            AttributesItems::class,
            AnnotationsJsonContent::class,
            AttributesJsonContent::class,
            AnnotationsProperty::class,
            AttributesProperty::class,
            AnnotationSchema::class,
            AttributeSchema::class,
            AttributesServerVariable::class,
            AnnotationsServerVariable::class,
            AnnotationsXmlContent::class,
            AttributesXmlContent::class,
        ], true);

        foreach ($schemas as $schema) {
            if ($schema->enum !== Generator::UNDEFINED && is_string($schema->enum)) {
                $source = $schema->enum;
                // Convert to Enum value if it is an Enum class string
                if (is_a($schema->enum, 'UnitEnum', true)) {
                    $enums = [];
                    foreach ($source::cases() as $case) {
                        $enums[] = $case->value ?? $case->name;
                    }

                    $schema->enum = $enums;
                } else {
                    throw new \InvalidArgumentException("Unexpected enum value, requires specifying the Enum class string: $source");
                }
            }
        }
    }
}
