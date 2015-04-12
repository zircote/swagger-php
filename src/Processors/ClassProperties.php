<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Property;
use Swagger\Annotations\Swagger;
use Swagger\Annotations\Definition;
use Swagger\Annotations\Items;
use Swagger\Context;

/**
 * Use the property context to extract useful information and inject that into the annotation.
 */
class ClassProperties {

    static $types = [
        'array' => 'array',
        'byte' => ['string', 'byte'],
        'boolean' => 'boolean',
        'bool' => 'boolean',
        'int' => 'integer',
        'integer' => 'integer',
        'long' => ['integer', 'long'],
        'float' => ['number', 'float'],
        'double' => ['number', 'double'],
        'string' => 'string',
        'date' => ['string', 'date'],
        'datetime' => ['string', 'date-time'],
        '\\datetime' => ['string', 'date-time'],
        'byte' => ['string', 'byte'],
        'number' => 'number',
        'object' => 'object'
    ];

    public function __invoke(Swagger $swagger) {
        $refs = [];
        // Use the class names for @SWG\Definition()
        foreach ($swagger->definitions as $definition) {
            if ($definition->name === null && $definition->_context->is('class')) {
                $definition->name = $definition->_context->class;
                // if ($definition->type === null) {
                //     $definition->type = 'object';
                // }
            }
            $refs[strtolower($definition->_context->fullyQualifiedName($definition->_context->class))] = '#/definitions/'.$definition->name;
        }
        // Merge @SWG\Property() for php properties into the @SWG\Definition of the class.
        foreach ($swagger->_unmerged as $i => $property) {
            if ($property instanceof Property && $property->_context->is('property')) {
                $classAnnotations = $property->_context->with('class')->annotations;
                $notFound = true;
                foreach ($classAnnotations as $annotation) {
                    if ($annotation instanceof Definition) {
                        $annotation->merge([$property]);
                        unset($swagger->_unmerged[$i]);
                        $notFound = false;
                        break;
                    }
                }
                if ($notFound) {
                    $this->processProperty($property, $refs);
                    // @todo lookup definition(s) based on inheritance
                }
            }
        }
        
        // Extract property info
        foreach ($swagger->definitions as $definition) {
            if ($definition->properties) {
                foreach ($definition->properties as $property) {
                    if ($property->_context->is('property')) { // Was this @SWG\Property defined in a docblock of a php-property?
                        $this->processProperty($property, $refs);
                    }
                }
            }
        }
    }

    /**
     * @param Property $annotation
     * @param array $refs
     */
    public function processProperty($annotation, $refs) {
        $context = $annotation->_context;
        // Use the property names for @SWG\Property()
        if ($annotation->name === null) {
            $annotation->name = $context->property;
        }

        if (preg_match('/@var\s+(?<type>[^\s]+)([ \t])?(?<description>.+)?$/im', $context->comment, $varMatches)) {
            if ($annotation->description === null && isset($varMatches['description'])) {
                $annotation->description = trim($varMatches['description']);
            }
            if ($annotation->type === null) {
                preg_match('/^([^\[]+)(.*$)/', trim($varMatches['type']), $typeMatches);
                $type = $typeMatches[1];

                if (array_key_exists(strtolower($type), static::$types)) {
                    $type = static::$types[strtolower($type)];
                    if (is_array($type)) {
                        if ($annotation->format === null) {
                            $annotation->format = $type[1];
                        }
                        $type = $type[0];
                    }
                    $annotation->type = $type;
                } elseif ($annotation->ref === null && $typeMatches[2] === '') {
                    $annotation->ref = @$refs[strtolower($annotation->_context->fullyQualifiedName($type))];
                }
                if ($typeMatches[2] === '[]') {
                    if ($annotation->items === null) {
                        $annotation->items = new Items([
                            'type' => $annotation->type,
                            '_context' => new Context(['generated' => true], $annotation->_context)
                        ]);
                        if ($annotation->items->type === null) {
                            $annotation->items->ref = @$refs[strtolower($annotation->_context->fullyQualifiedName($type))];
                        }
                    }
                    $annotation->type = 'array';
                }
            }
        }
        if ($annotation->description === null) {
            $annotation->description = $context->extractDescription();
        }
    }

}
