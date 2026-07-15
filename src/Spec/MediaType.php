<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

use OpenApi\Undefined;

/**
 * Describes the content payload for a specific media type.
 *
 * @see [Media Type Object](https://spec.openapis.org/oas/v3.1.1.html#media-type-object)
 */
#[\Attribute(\Attribute::IS_REPEATABLE)]
class MediaType extends AbstractAttribute
{
    /**
     * @param string|null                                $mediaType   The media type identifier (e.g. 'application/json')
     * @param Schema|null                                $schema      The schema defining the content
     * @param mixed                                      $example     Example of the media type content
     * @param list<Example>|null                         $examples    Examples of the media type content
     * @param list<Encoding>|array<string,Encoding>|null $encoding    Encoding information for specific properties
     * @param array<string,mixed>|null                   $x           Vendor extensions (x-* properties)
     * @param list<Attachable>|null                      $attachables Reusable custom attachable attributes
     */
    public function __construct(
        public ?string $mediaType = null,
        public ?Schema $schema = null,
        public mixed $example = Undefined::UNDEFINED,
        public ?array $examples = null,
        public ?array $encoding = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct(x: $x, attachables: $attachables);
    }

    public function merge(): array
    {
        return [
            Response::class => 'content[]',
            RequestBody::class => 'content[]',
            Parameter::class => 'content[]',
        ];
    }

    public function contains(): array
    {
        return [
            Encoding::class => 'encoding[]',
            Example::class => 'examples[]',
        ];
    }
}
