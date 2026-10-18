<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Adds metadata to a single tag used by the Operation Object.
 *
 * On a method or a class, the tag is also added to the operations declared there: a method's
 * own, or every operation of the class. On a class with no operations it only declares the tag.
 *
 * @see [Tag Object](https://spec.openapis.org/oas/v3.1.1.html#tag-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Tag extends AbstractAttribute
{
    /**
     * @param string|null                $name         The name of the tag
     * @param string|null                $summary      A short summary of the tag, used for display purposes
     * @param string|null                $description  A description of the tag (CommonMark syntax)
     * @param ExternalDocumentation|null $externalDocs Additional external documentation for this tag
     * @param string|null                $parent       The name of a tag that this tag is nested under
     * @param string|null                $kind         A machine-readable string to categorize the tag
     * @param array<string,mixed>|null   $x            Vendor extensions (x-* properties)
     * @param list<Attachable>|null      $attachables  Reusable custom attachable attributes
     * @param Fan|null                   $fan          Where this attribute goes when more than one attribute could hold it
     */
    public function __construct(
        public ?string $name = null,
        public ?string $summary = null,
        public ?string $description = null,
        public ?ExternalDocumentation $externalDocs = null,
        public ?string $parent = null,
        public ?string $kind = null,
        ?array $x = null,
        ?array $attachables = null,
        ?Fan $fan = null,
    ) {
        parent::__construct(x: $x, attachables: $attachables, fan: $fan);
    }

    public function isRoot(): bool
    {
        return true;
    }
}
