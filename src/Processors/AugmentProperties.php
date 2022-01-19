<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations\Components;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\Schema;
use OpenApi\Context;
use OpenApi\Generator;
use OpenApi\Util;

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
        '\\datetime' => ['string', 'date-time'],
        'datetimeimmutable' => ['string', 'date-time'],
        '\\datetimeimmutable' => ['string', 'date-time'],
        'datetimeinterface' => ['string', 'date-time'],
        '\\datetimeinterface' => ['string', 'date-time'],
        'number' => 'number',
        'object' => 'object',
    ];

    public function __invoke(Analysis $analysis)
    {
        $refs = [];
        if (!Generator::isDefault($analysis->openapi->components) && !Generator::isDefault($analysis->openapi->components->schemas)) {
            foreach ($analysis->openapi->components->schemas as $schema) {
                if (!Generator::isDefault($schema->schema)) {
                    $refKey = $this->toRefKey($schema->_context, $schema->_context->class);
                    $refs[$refKey] = Components::ref($schema);
                }
            }
        }

        /** @var Property[] $properties */
        $properties = $analysis->getAnnotationsOfType(Property::class);

        foreach ($properties as $property) {
            $context = $property->_context;

            if (Generator::isDefault($property->property)) {
                $property->property = $context->property;
            }

            if (!Generator::isDefault($property->ref)) {
                continue;
            }

            $comment = str_replace("\r\n", "\n", (string) $context->comment);
            preg_match('/@var\s+(?<type>[^\s]+)([ \t])?(?<description>.+)?$/im', $comment, $varMatches);

            if (Generator::isDefault($property->type)) {
                $this->augmentType($analysis, $property, $context, $refs, $varMatches);
            } else {
                Util::mapNativeType($property, $property->type);
            }

            if (Generator::isDefault($property->description) && isset($varMatches['description'])) {
                $property->description = trim($varMatches['description']);
            }
            if (Generator::isDefault($property->description) && $property->isRoot()) {
                $property->description = $context->phpdocContent();
            }

            if (Generator::isDefault($property->example) && preg_match('/@example\s+([ \t])?(?<example>.+)?$/im', $comment, $varMatches)) {
                $property->example = $varMatches['example'];
            }
        }
    }

    protected function toRefKey(Context $context, $name)
    {
        $fqn = strtolower($context->fullyQualifiedName($name));

        return ltrim($fqn, '\\');
    }

    protected function augmentType(Analysis $analysis, Property $property, Context $context, array $refs, array $varMatches)
    {
        // docblock typehints
        if (isset($varMatches['type'])) {
            $allTypes = strtolower(trim($varMatches['type']));

            if ($this->isNullable($allTypes) && Generator::isDefault($property->nullable)) {
                $property->nullable = true;
            }

            $allTypes = $this->stripNull($allTypes);
            preg_match('/^([^\[]+)(.*$)/', $allTypes, $typeMatches);
            $type = $typeMatches[1];

            // finalise property type/ref
            if (!Util::mapNativeType($property, $type)) {
                $refKey = $this->toRefKey($context, $type);
                if (Generator::isDefault($property->ref) && array_key_exists($refKey, $refs)) {
                    $property->ref = $refs[$refKey];
                }
            }

            // ok, so we possibly have a type or ref
            if (!Generator::isDefault($property->ref) && $typeMatches[2] === '' && $property->nullable) {
                $refKey = $this->toRefKey($context, $type);
                $property->oneOf = [
                    new Schema([
                        'ref' => $refs[$refKey],
                        '_context' => $property->_context,
                        '_aux' => true,
                    ]),
                ];
                $property->nullable = true;
            } elseif ($typeMatches[2] === '[]') {
                if (Generator::isDefault($property->items)) {
                    $property->items = new Items(
                        [
                            'type' => $property->type,
                            '_context' => new Context(['generated' => true], $context),
                            '_aux' => true,
                        ]
                    );
                    if (!Generator::isDefault($property->ref)) {
                        $property->items->ref = $property->ref;
                        $property->ref = Generator::UNDEFINED;
                    }
                    $property->type = 'array';
                }
            }
        }

        // native typehints
        if ($context->type && !Generator::isDefault($context->type)) {
            if ($context->nullable === true) {
                $property->nullable = true;
            }
            $type = strtolower($context->type);
            if (!Util::mapNativeType($property, $type)) {
                $refKey = $this->toRefKey($context, $type);
                if (Generator::isDefault($property->ref) && array_key_exists($refKey, $refs)) {
                    $this->applyRef($property, $refs[$refKey]);
                } else {
                    if ($typeSchema = $analysis->getSchemaForSource($context->type)) {
                        if (Generator::isDefault($property->format)) {
                            $property->ref = Components::ref($typeSchema);
                            $property->type = Generator::UNDEFINED;
                        }
                    }
                }
            }
        }
    }

    protected function isNullable(string $typeDescription): bool
    {
        return in_array('null', explode('|', strtolower($typeDescription)));
    }

    protected function stripNull(string $typeDescription): string
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

    protected function applyRef(Property $property, string $ref): void
    {
        if ($property->nullable === true) {
            $property->oneOf = [
                new Schema([
                    'ref' => $ref,
                    '_context' => $property->_context,
                    '_aux' => true,
                ]),
            ];
        } else {
            $property->ref = $ref;
        }
    }
}
