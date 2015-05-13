<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Items;
use Swagger\Annotations\Property;
use Swagger\ClassAnnotations;
use Swagger\Utils\DocComment;

/**
 * Sets the property type from the comment var tag.
 *
 * Supports scalar types and basic non-scalar use cases.
 */
class PropertyTypeComment
{
    public function __invoke(ClassAnnotations $annotations)
    {
        foreach ($annotations->propertyAnnotations as $property) {
            foreach ($property->getSwaggerAnnotations() as $annotation) {
                $this->processAnnotation($property->target, $annotation);
            }
        }
    }

    private function processAnnotation(\ReflectionProperty $property, Property $annotation)
    {
        if ($annotation->type) {
            return;
        }
        $varType = (new DocComment($property->getDocComment()))->getTag('var');
        if ($varType === null) {
            return;
        }

        $isArray   = substr($varType, -2) === '[]';
        $innerType = $isArray ? substr($varType, 0, -2) : $varType;

        $swaggerType = isset(ClassProperties::$types[strtolower($innerType)])
            ? ClassProperties::$types[strtolower($innerType)]
            : null;

        if ($isArray) {
            $annotation->type  = 'array';
            $annotation->items = $annotation->items ?: new Items([]);
        }

        $this->setScalarType($innerType, $swaggerType, $isArray ? $annotation->items : $annotation);
    }

    private function setScalarType($innerType, $swaggerType, $target)
    {
        if ($swaggerType && is_array($swaggerType)) {
            list($target->type, $target->format) = $swaggerType;
        } elseif ($swaggerType) {
            $target->type = $swaggerType;
        } else {
            $target->ref = '#/definitions/' . $innerType;
        }
    }
}
