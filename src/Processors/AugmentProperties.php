<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Annotations\Schema;
use OpenApi\Analysis;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;
use OpenApi\Context;

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
        'object' => 'object'
    ];

    public function __invoke(Analysis $analysis)
    {
        $refs = [];
        if ($analysis->openapi->components && $analysis->openapi->components->schemas) {
            /** @var Schema $schema */
            foreach ($analysis->openapi->components->schemas as $schema) {
                if ($schema->schema) {
                    $refs[strtolower($schema->_context->fullyQualifiedName($schema->_context->class))] = '#/components/schemas/' . $schema->schema;
                }
            }
        }

        $allProperties = $analysis->getAnnotationsOfType(Property::class);
        /** @var \OpenApi\Annotations\Property $property */
        foreach ($allProperties as $property) {
            $context = $property->_context;
            // Use the property names for @OA\Property()
            if ($property->property === null) {
                $property->property = $context->property;
            }

            if (preg_match('/@var\s+(?<type>[^\s]+)([ \t])?(?<description>.+)?$/im', $context->comment, $varMatches)) {
                if ($property->description === null && isset($varMatches['description'])) {
                    $property->description = trim($varMatches['description']);
                }
                if ($property->type === null) {
                    preg_match('/^([^\[]+)(.*$)/', trim($varMatches['type']), $typeMatches);
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

            if ($property->example === null && preg_match('/@example\s+([ \t])?(?<example>.+)?$/im', $context->comment, $varMatches)) {
                $property->example = $varMatches['example'];
            }

            if ($property->description === null && empty($property->ref)) {
                $property->description = $context->phpdocContent();
            }
        }
    }
}
