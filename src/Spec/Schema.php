<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

use OpenApi\Undefined;

/**
 * Defines the structure and validation rules for a data type.
 *
 * Based on JSON Schema with OpenAPI-specific extensions. Can represent objects,
 * primitives, and arrays.
 *
 * @see [Schema Object](https://spec.openapis.org/oas/v3.1.1.html#schema-object)
 * @see [JSON Schema](https://json-schema.org/draft/2020-12/json-schema-validation)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class Schema extends AbstractAttribute
{
    /**
     * @param string|null                           $schema                Reusable schema identifier (component key)
     * @param string|null                           $title                 A title for the schema
     * @param string|null                           $description           A description of the schema (CommonMark syntax)
     * @param string|null                           $ref                   A JSON Reference to a reusable schema
     * @param string|list<string>|null              $type                  The value type(s) (string, number, integer, boolean, array, object, null)
     * @param string|null                           $format                Further refines the type (e.g. int32, int64, float, double, date-time, email)
     * @param bool|null                             $nullable              Whether the value can be null (OAS 3.0 only; use type array in 3.1+)
     * @param int|null                              $minLength             Minimum string length
     * @param int|null                              $maxLength             Maximum string length
     * @param string|null                           $pattern               Regular expression pattern the string must match
     * @param string|null                           $contentMediaType      The media type of string content encoding
     * @param string|null                           $contentEncoding       The encoding used for string content (e.g. base64)
     * @param int|float|null                        $minimum               Minimum numeric value (inclusive)
     * @param int|float|null                        $maximum               Maximum numeric value (inclusive)
     * @param int|float|bool|null                   $exclusiveMinimum      Exclusive minimum value
     * @param int|float|bool|null                   $exclusiveMaximum      Exclusive maximum value
     * @param int|float|null                        $multipleOf            The value must be a multiple of this number
     * @param Schema|string|null                    $items                 Schema for array items
     * @param int|null                              $minItems              Minimum number of array items
     * @param int|null                              $maxItems              Maximum number of array items
     * @param bool|null                             $uniqueItems           Whether array items must be unique
     * @param list<Schema>|null                     $prefixItems           Schemas for positional array items (tuple validation)
     * @param Schema|bool|null                      $contains              Schema that at least one array item must match
     * @param int|null                              $minContains           Minimum number of items matching contains
     * @param int|null                              $maxContains           Maximum number of items matching contains
     * @param Schema|bool|null                      $unevaluatedItems      Schema for items not covered by other keywords
     * @param list<Property|Schema>|null            $properties            Object property definitions
     * @param list<string>|null                     $required              List of required property names
     * @param Schema|bool|null                      $additionalProperties  Schema or boolean for additional properties
     * @param array<string,Schema>|null             $patternProperties     Schemas for properties matching regex patterns
     * @param int|null                              $minProperties         Minimum number of properties
     * @param int|null                              $maxProperties         Maximum number of properties
     * @param Schema|bool|null                      $unevaluatedProperties Schema for properties not covered by other keywords
     * @param Schema|null                           $propertyNames         Schema that property names must validate against
     * @param array<string,list<string>>|null       $dependentRequired     Property-level required dependencies
     * @param array<string,Schema>|null             $dependentSchemas      Property-level schema dependencies
     * @param list<Schema>|null                     $allOf                 All schemas must match (AND composition)
     * @param list<Schema>|null                     $anyOf                 At least one schema must match (OR composition)
     * @param list<Schema>|null                     $oneOf                 Exactly one schema must match (XOR composition)
     * @param Schema|null                           $not                   The schema must NOT match
     * @param Schema|null                           $if                    Conditional schema (if-then-else)
     * @param Schema|null                           $then                  Applied when 'if' succeeds
     * @param Schema|null                           $else                  Applied when 'if' fails
     * @param list<string|int|float|bool|null>|null $enum                  Allowed values
     * @param mixed                                 $const                 A single allowed value
     * @param mixed                                 $example               An example value
     * @param list<mixed>|null                      $examples              A list of example values
     * @param bool|null                             $deprecated            Whether the schema is deprecated
     * @param bool|null                             $readOnly              Whether the value is read-only
     * @param bool|null                             $writeOnly             Whether the value is write-only
     * @param mixed                                 $default               The default value
     * @param Discriminator|null                    $discriminator         Discriminator for polymorphism
     * @param ExternalDocumentation|null            $externalDocs          Additional external documentation
     * @param Xml|null                              $xml                   XML representation metadata
     * @param array<string,mixed>|null              $x                     Vendor extensions (x-* properties)
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

    public function isRoot(): bool
    {
        return true;
    }

    public function merge(): array
    {
        return [
            Property::class => 'schema',
            Parameter::class => 'schema',
            Header::class => 'schema',
            MediaType::class => 'schema',
        ];
    }

    public function contains(): array
    {
        return [
            Property::class => 'properties[]',
            self::class => 'properties[]',
        ];
    }
}
