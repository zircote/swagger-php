<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

use OpenApi\Undefined;

/**
 * Describes an example value for a parameter, media type, or schema.
 *
 * @see [Example Object](https://spec.openapis.org/oas/v3.1.1.html#example-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class Example extends AbstractAttribute
{
    /** The key this attribute is filed under in `components`; null for an inline one. */
    public ?string $component = null;

    /**
     * @param string|null              $example       The example name — the key the example nests under in a media type, parameter or header
     * @param string|null              $summary       Short description of the example
     * @param string|null              $description   Long description of the example (CommonMark syntax)
     * @param mixed                    $value         Embedded literal example value
     * @param string|null              $externalValue A URI pointing to the literal example
     * @param string|null              $ref           A JSON Reference to a reusable example
     * @param string|null              $component     The key this is filed under in `components`, which makes it a reusable component
     * @param array<string,mixed>|null $x             Vendor extensions (x-* properties)
     * @param list<Attachable>|null    $attachables   Reusable custom attachable attributes
     */
    public function __construct(
        public ?string $example = null,
        public ?string $summary = null,
        public ?string $description = null,
        public mixed $value = Undefined::UNDEFINED,
        public ?string $externalValue = null,
        public string|Schema\Ref|null $ref = null,
        ?string $component = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct(x: $x, attachables: $attachables);
        $this->component = $component;
    }

    public function isRoot(): bool
    {
        return $this->component !== null;
    }

    public function merge(): array
    {
        return [
            Components::class => 'examples[]',
            MediaType::class => 'examples[]',
            Parameter::class => 'examples[]',
            Header::class => 'examples[]',
        ];
    }

    public function contained(): array
    {
        return [
            MediaType::class => 'examples[]',
            Parameter::class => 'examples[]',
            Header::class => 'examples[]',
        ];
    }
}
