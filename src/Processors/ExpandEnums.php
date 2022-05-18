<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations\Schema as AnnotationSchema;
use OpenApi\Attributes\Schema as AttributeSchema;
use OpenApi\Generator;
use OpenApi\Util;

/**
 * Look at all enums with a schema and:
 * - set the name `schema`
 * - set `enum` values.
 */
class ExpandEnums
{
    public function __invoke(Analysis $analysis)
    {
        if (!class_exists('\\ReflectionEnum')) {
            return;
        }

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
                Util::mapNativeType($schema, $type);
            }
        }
    }
}
