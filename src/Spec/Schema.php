<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

use OpenApi\Undefined;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class Schema extends AbstractAttribute
{
    /**
     * @param string|list<string>|null              $type
     * @param list<Schema>|null                     $prefixItems
     * @param list<Property|Schema>|null            $properties
     * @param list<string>|null                     $required
     * @param array<string,Schema>|null             $patternProperties
     * @param array<string,list<string>>|null       $dependentRequired
     * @param array<string,Schema>|null             $dependentSchemas
     * @param list<Schema>|null                     $allOf
     * @param list<Schema>|null                     $anyOf
     * @param list<Schema>|null                     $oneOf
     * @param list<string|int|float|bool|null>|null $enum
     * @param list<mixed>|null                      $examples
     * @param array<string,mixed>|null              $x
     */
    public function __construct(
        // Identity
        public ?string $schema = null,
        public ?string $title = null,
        public ?string $description = null,

        // Reference
        public ?string $ref = null,

        // Core type
        public string|array|null $type = null,
        public ?string $format = null,
        public ?bool $nullable = null,

        // String constraints
        public ?int $minLength = null,
        public ?int $maxLength = null,
        public ?string $pattern = null,
        public ?string $contentMediaType = null,
        public ?string $contentEncoding = null,

        // Numeric constraints
        public int|float|null $minimum = null,
        public int|float|null $maximum = null,
        public int|float|bool|null $exclusiveMinimum = null,
        public int|float|bool|null $exclusiveMaximum = null,
        public int|float|null $multipleOf = null,

        // Array constraints
        public Schema|string|null $items = null,
        public ?int $minItems = null,
        public ?int $maxItems = null,
        public ?bool $uniqueItems = null,
        public ?array $prefixItems = null,
        public Schema|bool|null $contains = null,
        public ?int $minContains = null,
        public ?int $maxContains = null,
        public Schema|bool|null $unevaluatedItems = null,

        // Object constraints
        public ?array $properties = null,
        public ?array $required = null,
        public Schema|bool|null $additionalProperties = null,
        public ?array $patternProperties = null,
        public ?int $minProperties = null,
        public ?int $maxProperties = null,
        public Schema|bool|null $unevaluatedProperties = null,
        public ?Schema $propertyNames = null,
        public ?array $dependentRequired = null,
        public ?array $dependentSchemas = null,

        // Composition
        public ?array $allOf = null,
        public ?array $anyOf = null,
        public ?array $oneOf = null,
        public ?Schema $not = null,

        // Conditional
        public ?Schema $if = null,
        public ?Schema $then = null,
        public ?Schema $else = null,

        // Enum/const
        public ?array $enum = null,
        public mixed $const = Undefined::UNDEFINED,

        // Examples
        public mixed $example = Undefined::UNDEFINED,
        public ?array $examples = null,

        // Meta
        public ?bool $deprecated = null,
        public ?bool $readOnly = null,
        public ?bool $writeOnly = null,
        public mixed $default = Undefined::UNDEFINED,

        // OpenAPI extensions on Schema
        public ?Discriminator $discriminator = null,
        public ?ExternalDocumentation $externalDocs = null,
        public ?Xml $xml = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function allowedParents(): ?array
    {
        return [Parameter::class, Header::class, MediaType::class];
    }
}
