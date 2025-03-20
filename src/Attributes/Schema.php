<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;
use OpenApi\Annotations as OA;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Schema extends OA\Schema
{
    /**
     * @param string|class-string|object|null                               $ref
     * @param string[]                                                      $required
     * @param Property[]                                                    $properties
     * @param string|non-empty-array<string>|null                           $type
     * @param int|float                                                     $maximum
     * @param int|float                                                     $minimum
     * @param array<string|int|float|bool|\UnitEnum|null>|class-string|null $enum
     * @param array<Examples>                                               $examples
     * @param array<Schema|OA\Schema>                                       $allOf
     * @param array<Schema|OA\Schema>                                       $anyOf
     * @param array<Schema|OA\Schema>                                       $oneOf
     * @param array<string,mixed>|null                                      $x
     * @param Attachable[]|null                                             $attachables
     */
    public function __construct(
        // schema
        string|object|null $ref = null,
        ?string $schema = null,
        ?string $title = null,
        ?string $description = null,
        ?int $maxProperties = null,
        ?int $minProperties = null,
        ?array $required = null,
        ?array $properties = null,
        string|array|null $type = null,
        ?string $format = null,
        ?Items $items = null,
        ?string $collectionFormat = null,
        mixed $default = Generator::UNDEFINED,
        $maximum = null,
        bool|int|float|null $exclusiveMaximum = null,
        $minimum = null,
        bool|int|float|null $exclusiveMinimum = null,
        ?int $maxLength = null,
        ?int $minLength = null,
        ?int $maxItems = null,
        ?int $minItems = null,
        ?bool $uniqueItems = null,
        ?string $pattern = null,
        array|string|null $enum = null,
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
        AdditionalProperties|bool|null $additionalProperties = null,
        mixed $const = Generator::UNDEFINED,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
            'ref' => $ref ?? Generator::UNDEFINED,
            'schema' => $schema ?? Generator::UNDEFINED,
            'title' => $title ?? Generator::UNDEFINED,
            'description' => $description ?? Generator::UNDEFINED,
            'maxProperties' => $maxProperties ?? Generator::UNDEFINED,
            'minProperties' => $minProperties ?? Generator::UNDEFINED,
            'required' => $required ?? Generator::UNDEFINED,
            'properties' => $properties ?? Generator::UNDEFINED,
            'type' => $type ?? Generator::UNDEFINED,
            'format' => $format ?? Generator::UNDEFINED,
            'collectionFormat' => $collectionFormat ?? Generator::UNDEFINED,
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
            'pattern' => $pattern ?? Generator::UNDEFINED,
            'enum' => $enum ?? Generator::UNDEFINED,
            'readOnly' => $readOnly ?? Generator::UNDEFINED,
            'writeOnly' => $writeOnly ?? Generator::UNDEFINED,
            'xml' => $xml ?? Generator::UNDEFINED,
            'example' => $example,
            'nullable' => $nullable ?? Generator::UNDEFINED,
            'deprecated' => $deprecated ?? Generator::UNDEFINED,
            'allOf' => $allOf ?? Generator::UNDEFINED,
            'anyOf' => $anyOf ?? Generator::UNDEFINED,
            'oneOf' => $oneOf ?? Generator::UNDEFINED,
            'additionalProperties' => $additionalProperties ?? Generator::UNDEFINED,
            'const' => $const,
            'x' => $x ?? Generator::UNDEFINED,
            'attachables' => $attachables ?? Generator::UNDEFINED,
            'value' => $this->combine($items, $discriminator, $externalDocs, $examples),
        ]);
    }
}
