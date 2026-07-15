<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Allows referencing an external resource for extended documentation.
 *
 * @see [External Documentation Object](https://spec.openapis.org/oas/v3.1.1.html#external-documentation-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ExternalDocumentation extends AbstractAttribute
{
    /**
     * @param string|null              $url         The URL for the target documentation
     * @param string|null              $description A description of the target documentation (CommonMark syntax)
     * @param array<string,mixed>|null $x           Vendor extensions (x-* properties)
     * @param list<Attachable>|null    $attachables Reusable custom attachable attributes
     */
    public function __construct(
        public ?string $url = null,
        public ?string $description = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct(x: $x, attachables: $attachables);
    }

    public function isRoot(): bool
    {
        return true;
    }

    public function merge(): array
    {
        return [
            Operation::class => 'externalDocs',
            Schema::class => 'externalDocs',
        ];
    }
}
