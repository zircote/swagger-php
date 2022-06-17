<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Definition;
use Swagger\Annotations\Items;
use Swagger\Annotations\Property;
use Swagger\Context;
use Swagger\Analysis;

/**
 * Use the property context to extract useful information and inject that into the annotation.
 */
class AugmentProperties
{
    public static $types = [
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
        '\datetime' => ['string', 'date-time'],
        'datetimeimmutable' => ['string', 'date-time'],
        '\datetimeimmutable' => ['string', 'date-time'],
        'datetimeinterface' => ['string', 'date-time'],
        '\datetimeinterface' => ['string', 'date-time'],
        'number' => 'number',
        'object' => 'object',
    ];

    public function __invoke(Analysis $analysis)
    {
        $refs = [];
        /** @var Definition $definition */
        foreach ($analysis->swagger->definitions as $definition) {
            if ($definition->definition) {
                $refs[strtolower($definition->_context->fullyQualifiedName($definition->_context->class))] = '#/definitions/' . $definition->definition;
            }
        }
        
        $allProperties = $analysis->getAnnotationsOfType(Property::class);
        /** @var Property $property */
        foreach ($allProperties as $property) {
            $context = $property->_context;
            // Use the property names for @SWG\Property()
            if ($property->property === null) {
                $property->property = $context->property;
            }

            if (preg_match('/@var\s+(?<type>[^\s]+)([ \t])?(?<description>.+)?$/im', (string) $context->comment, $varMatches)) {
                if ($property->description === null && isset($varMatches['description'])) {
                    $property->description = trim($varMatches['description']);
                }
                if ($property->type === null) {
                    preg_match('/^([^\[]+)(.*$)/', $this->stripNull(trim($varMatches['type'])), $typeMatches);
                    $type = $typeMatches[1];

                    if (array_key_exists(strtolower($type), static::$types)) {
                        $type = static::$types[strtolower($type)];
                        if (is_array($type)) {
                            if ($property->format === null) {
                                $property->format = $type[1];
                            }
                            $type = $type[0];
                        }
                        $property->type = $type;
                    } elseif ($property->ref === null && $typeMatches[2] === '') {
                        $tmpKey = strtolower($context->fullyQualifiedName($type));
                        $property->ref = array_key_exists($tmpKey, $refs) ? $refs[$tmpKey] : null;
                    }
                    if ($typeMatches[2] === '[]') {
                        if ($property->items === null) {
                            $property->items = new Items([
                                'type' => $property->type,
                                '_context' => new Context(['generated' => true], $context)
                            ]);
                            if ($property->items->type === null) {
                                $tmpKey = strtolower($context->fullyQualifiedName($type));
                                $property->items->ref = array_key_exists($tmpKey, $refs) ? $refs[$tmpKey] : null;
                            }
                        }
                        $property->type = 'array';
                    }
                }
            }
            if ($property->description === null) {
                $property->description = $context->phpdocContent();
            }
        }
    }

    /**
     * @param string $typeDescription
     *
     * @return string
     */
    protected function stripNull($typeDescription)
    {
        if (strpos($typeDescription, '|') === false) {
            return $typeDescription;
        }
        $types = [];
        foreach (explode('|', $typeDescription) as $type) {
            if (strtolower($type) === 'null') {
                continue;
            }
            $types[] = $type;
        }
        return implode('|', $types);
    }
}
