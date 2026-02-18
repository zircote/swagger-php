<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;
use OpenApi\Annotations as OA;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::TARGET_CLASS_CONSTANT | \Attribute::IS_REPEATABLE)]
class Property extends OA\Property
{
    /**
     * @param string|class-string|object|null                              $ref
     * @param list<string>                                                 $required
     * @param list<Property>                                               $properties
     * @param string|non-empty-array<string>|null                          $type
     * @param array<Examples>                                              $examples
     * @param array<Schema|OA\Schema>                                      $allOf
     * @param array<Schema|OA\Schema>                                      $anyOf
     * @param array<Schema|OA\Schema>                                      $oneOf
     * @param list<string|int|float|bool|\UnitEnum|null>|class-string|null $enum
     * @param array<string,mixed>|null                                     $x
     * @param list<Attachable>|null                                        $attachables
     */
    public function __construct(
        ?string $property = null,
        ?Encoding $encoding = null,

        // Schema
        string|object|null $ref = null,
        ?string $schema = null,
        ?string $title = null,
        ?string $description = Generator::UNDEFINED,
        ?int $maxProperties = null,
        ?int $minProperties = null,
        ?array $required = null,
        ?array $properties = null,
        string|array|null $type = null,
        ?string $format = null,
        ?Items $items = null,
        ?string $collectionFormat = null,
        ?string $pattern = null,
        ?Discriminator $discriminator = null,
        ?bool $readOnly = null,
        ?bool $writeOnly = null,
        ?Xml $xml = null,
        ?ExternalDocumentation $externalDocs = null,
        mixed $example = Generator::UNDEFINED,
        ?array $examples = null,
        ?bool $nullable = null,
        ?bool $deprecated = null,
        ?array $allOf = null,
        ?array $anyOf = null,
        ?array $oneOf = null,
        ?string $contentEncoding = null,
        ?string $contentMediaType = null,

        // JSON Schema
        mixed $default = Generator::UNDEFINED,
        int|float|null $maximum = null,
        bool|int|float|null $exclusiveMaximum = null,
        int|float|null $minimum = null,
        bool|int|float|null $exclusiveMinimum = null,
        int|null $maxLength = null,
        int|null $minLength = null,
        int|null $maxItems = null,
        int|null $minItems = null,
        bool|null $uniqueItems = null,
        array|string|null $enum = null,
        mixed $not = Generator::UNDEFINED,
        bool|AdditionalProperties|null $additionalProperties = null,
        array|null $additionalItems = null,
        array|null $contains = null,
        array|null $patternProperties = null,
        array|null $unevaluatedProperties = null,
        mixed $dependencies = Generator::UNDEFINED,
        mixed $propertyNames = Generator::UNDEFINED,
        mixed $const = Generator::UNDEFINED,

        // abstract annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
            'property' => $property ?? Generator::UNDEFINED,

            // Schema
            'ref' => $ref ?? Generator::UNDEFINED,
            'schema' => $schema ?? Generator::UNDEFINED,
            'title' => $title ?? Generator::UNDEFINED,
            'description' => $description,
            'maxProperties' => $maxProperties ?? Generator::UNDEFINED,
            'minProperties' => $minProperties ?? Generator::UNDEFINED,
            'required' => $required ?? Generator::UNDEFINED,
            'properties' => $properties ?? Generator::UNDEFINED,
            'type' => $type ?? Generator::UNDEFINED,
            'format' => $format ?? Generator::UNDEFINED,
            'collectionFormat' => $collectionFormat ?? Generator::UNDEFINED,
            'pattern' => $pattern ?? Generator::UNDEFINED,
            'readOnly' => $readOnly ?? Generator::UNDEFINED,
            'writeOnly' => $writeOnly ?? Generator::UNDEFINED,
            'xml' => $xml ?? Generator::UNDEFINED,
            'example' => $example,
            'nullable' => $nullable ?? Generator::UNDEFINED,
            'deprecated' => $deprecated ?? Generator::UNDEFINED,
            'allOf' => $allOf ?? Generator::UNDEFINED,
            'anyOf' => $anyOf ?? Generator::UNDEFINED,
            'oneOf' => $oneOf ?? Generator::UNDEFINED,
            'contentEncoding' => $contentEncoding ?? Generator::UNDEFINED,
            'contentMediaType' => $contentMediaType ?? Generator::UNDEFINED,

            // JSON Schema
            'default' => $default,
            'maximum' => $maximum ?? Generator::UNDEFINED,
            'exclusiveMaximum' => $exclusiveMaximum ?? Generator::UNDEFINED,
            'minimum' => $minimum ?? Generator::UNDEFINED,
            'exclusiveMinimum' => $exclusiveMinimum ?? Generator::UNDEFINED,
            'maxLength' => $maxLength ?? Generator::UNDEFINED,
            'minLength' => $minLength ?? Generator::UNDEFINED,
            'maxItems' => $maxItems ?? Generator::UNDEFINED,
            'minItems' => $minItems ?? Generator::UNDEFINED,
            'uniqueItems' => $uniqueItems ?? Generator::UNDEFINED,
            'enum' => $enum ?? Generator::UNDEFINED,
            'not' => $not,
            'additionalProperties' => $additionalProperties ?? Generator::UNDEFINED,
            'additionalItems' => $additionalItems ?? Generator::UNDEFINED,
            'contains' => $contains ?? Generator::UNDEFINED,
            'patternProperties' => $patternProperties ?? Generator::UNDEFINED,
            'unevaluatedProperties' => $unevaluatedProperties ?? Generator::UNDEFINED,
            'dependencies' => $dependencies,
            'propertyNames' => $propertyNames,
            'const' => $const,

            // abstract annotation
            'x' => $x ?? Generator::UNDEFINED,
            'attachables' => $attachables ?? Generator::UNDEFINED,
            'value' => $this->combine($items, $discriminator, $externalDocs, $encoding, $examples),
        ]);
    }
}
