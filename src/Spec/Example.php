<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Describes an example value for a parameter, media type, or schema.
 *
 * @see [Example Object](https://spec.openapis.org/oas/v3.1.1.html#example-object)
 */
#[\Attribute(\Attribute::IS_REPEATABLE)]
class Example extends AbstractAttribute
{
    /**
     * @param string|null              $example       Reusable example identifier (component key)
     * @param string|null              $summary       Short description of the example
     * @param string|null              $description   Long description of the example (CommonMark syntax)
     * @param mixed                    $value         Embedded literal example value
     * @param string|null              $externalValue A URI pointing to the literal example
     * @param string|null              $ref           A JSON Reference to a reusable example
     * @param array<string,mixed>|null $x             Vendor extensions (x-* properties)
     */
    public function __construct(
        public ?string $example = null,
        public ?string $summary = null,
        public ?string $description = null,
        public mixed $value = null,
        public ?string $externalValue = null,
        public ?string $ref = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function merge(): array
    {
        return [
            MediaType::class => 'examples[]',
            Parameter::class => 'examples[]',
            Header::class => 'examples[]',
        ];
    }
}
