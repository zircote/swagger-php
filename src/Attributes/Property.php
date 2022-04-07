<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::TARGET_CLASS_CONSTANT)]
class Property extends \OpenApi\Annotations\Property
{
    /**
     * @param string[]                  $required
     * @param Property[]                $properties
     * @param array<string,string>|null $x
     * @param Attachable[]|null         $attachables
     */
    public function __construct(
        ?string $property = null,
        // schema
        string|object|null $ref = null,
        ?string $schema = null,
        ?string $title = null,
        ?string $description = null,
        ?array $required = null,
        ?array $properties = null,
        ?string $type = null,
        ?string $format = null,
        ?Items $items = null,
        ?string $collectionFormat = null,
        $default = Generator::UNDEFINED,
        $maximum = null,
        ?bool $exclusiveMaximum = null,
        $minimum = null,
        ?bool $exclusiveMinimum = null,
        ?int $maxLength = null,
        ?int $minLength = null,
        ?int $maxItems = null,
        ?int $minItems = null,
        ?bool $uniqueItems = null,
        ?string $pattern = null,
        ?array $enum = null,
        ?Discriminator $discriminator = null,
        ?bool $readOnly = null,
        ?bool $writeOnly = null,
        ?Xml $xml = null,
        ?ExternalDocumentation $externalDocs = null,
        $example = Generator::UNDEFINED,
        ?bool $nullable = null,
        ?bool $deprecated = null,
        ?array $allOf = null,
        ?array $anyOf = null,
        ?array $oneOf = null,
        AdditionalProperties|bool|null $additionalProperties = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
            'property' => $property ?? Generator::UNDEFINED,
            // schema
            'ref' => $ref ?? Generator::UNDEFINED,
            'schema' => $schema ?? Generator::UNDEFINED,
            'title' => $title ?? Generator::UNDEFINED,
            'description' => $description ?? Generator::UNDEFINED,
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
            // annotation
            'x' => $x ?? Generator::UNDEFINED,
            'attachables' => $attachables ?? Generator::UNDEFINED,
            'value' => $this->combine($items, $discriminator, $externalDocs, $attachables),
        ]);
    }
}
