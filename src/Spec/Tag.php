<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Adds metadata to a single tag used by the Operation Object.
 *
 * @see [Tag Object](https://spec.openapis.org/oas/v3.1.1.html#tag-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Tag extends AbstractAttribute
{
    /**
     * @param string|null                $name         The name of the tag
     * @param string|null                $description  A description of the tag (CommonMark syntax)
     * @param ExternalDocumentation|null $externalDocs Additional external documentation for this tag
     * @param array<string,mixed>|null   $x            Vendor extensions (x-* properties)
     * @param list<Attachable>|null      $attachables  Reusable custom attachable attributes
     */
    public function __construct(
        public ?string $name = null,
        public ?string $description = null,
        public ?ExternalDocumentation $externalDocs = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct(x: $x, attachables: $attachables);
    }

    public function isRoot(): bool
    {
        return true;
    }

    public function contains(): array
    {
        return [ExternalDocumentation::class => 'externalDocs'];
    }
}
