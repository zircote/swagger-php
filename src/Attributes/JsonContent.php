<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Annotations as OA;
use OpenApi\Undefined;

/**
 * Shorthand for a json response.
 *
 * Example:
 * ```php
 * #[OA\JsonContent(
 *     ref: '#/components/schemas/user'
 * )]
 * ```
 * vs.
 * ```php
 * #[OA\MediaType(
 *     mediaType: 'application/json',
 *     schema: new OA\Schema(
 *         ref: '#/components/schemas/user'
 *     )
 * )
 * ```
 *
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class JsonContent extends OA\JsonContent
{
    /**
     * @param list<Encoding>                                               $encoding
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
        ?array $encoding = null,

        // Schema
        string|object|null $ref = null,
        ?string $schema = null,
        ?string $title = null,
        ?string $description = Undefined::UNDEFINED,
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
        mixed $example = Undefined::UNDEFINED,
        ?array $examples = null,
        ?bool $nullable = null,
        ?bool $deprecated = null,
        ?array $allOf = null,
        ?array $anyOf = null,
        ?array $oneOf = null,
        ?string $contentEncoding = null,
        ?string $contentMediaType = null,

        // JSON Schema
        mixed $default = Undefined::UNDEFINED,
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
        mixed $not = Undefined::UNDEFINED,
        bool|AdditionalProperties|null $additionalProperties = null,
        array|null $additionalItems = null,
        array|null $contains = null,
        array|null $patternProperties = null,
        array|null $unevaluatedProperties = null,
        mixed $dependencies = Undefined::UNDEFINED,
        mixed $propertyNames = Undefined::UNDEFINED,
        mixed $const = Undefined::UNDEFINED,

        // abstract annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
            // Schema
            'ref' => $ref ?? Undefined::UNDEFINED,
            'schema' => $schema ?? Undefined::UNDEFINED,
            'title' => $title ?? Undefined::UNDEFINED,
            'description' => $description,
            'maxProperties' => $maxProperties ?? Undefined::UNDEFINED,
            'minProperties' => $minProperties ?? Undefined::UNDEFINED,
            'required' => $required ?? Undefined::UNDEFINED,
            'properties' => $properties ?? Undefined::UNDEFINED,
            'type' => $type ?? Undefined::UNDEFINED,
            'format' => $format ?? Undefined::UNDEFINED,
            'collectionFormat' => $collectionFormat ?? Undefined::UNDEFINED,
            'pattern' => $pattern ?? Undefined::UNDEFINED,
            'readOnly' => $readOnly ?? Undefined::UNDEFINED,
            'writeOnly' => $writeOnly ?? Undefined::UNDEFINED,
            'xml' => $xml ?? Undefined::UNDEFINED,
            'example' => $example,
            'nullable' => $nullable ?? Undefined::UNDEFINED,
            'deprecated' => $deprecated ?? Undefined::UNDEFINED,
            'allOf' => $allOf ?? Undefined::UNDEFINED,
            'anyOf' => $anyOf ?? Undefined::UNDEFINED,
            'oneOf' => $oneOf ?? Undefined::UNDEFINED,
            'contentEncoding' => $contentEncoding ?? Undefined::UNDEFINED,
            'contentMediaType' => $contentMediaType ?? Undefined::UNDEFINED,

            // JSON Schema
            'default' => $default,
            'maximum' => $maximum ?? Undefined::UNDEFINED,
            'exclusiveMaximum' => $exclusiveMaximum ?? Undefined::UNDEFINED,
            'minimum' => $minimum ?? Undefined::UNDEFINED,
            'exclusiveMinimum' => $exclusiveMinimum ?? Undefined::UNDEFINED,
            'maxLength' => $maxLength ?? Undefined::UNDEFINED,
            'minLength' => $minLength ?? Undefined::UNDEFINED,
            'maxItems' => $maxItems ?? Undefined::UNDEFINED,
            'minItems' => $minItems ?? Undefined::UNDEFINED,
            'uniqueItems' => $uniqueItems ?? Undefined::UNDEFINED,
            'enum' => $enum ?? Undefined::UNDEFINED,
            'not' => $not,
            'additionalProperties' => $additionalProperties ?? Undefined::UNDEFINED,
            'additionalItems' => $additionalItems ?? Undefined::UNDEFINED,
            'contains' => $contains ?? Undefined::UNDEFINED,
            'patternProperties' => $patternProperties ?? Undefined::UNDEFINED,
            'unevaluatedProperties' => $unevaluatedProperties ?? Undefined::UNDEFINED,
            'dependencies' => $dependencies,
            'propertyNames' => $propertyNames,
            'const' => $const,

            // abstract annotation
            'x' => $x ?? Undefined::UNDEFINED,
            'attachables' => $attachables ?? Undefined::UNDEFINED,
            'value' => $this->combine($items, $discriminator, $externalDocs, $examples, $encoding),
        ]);
    }
}
