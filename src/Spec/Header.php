<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

use OpenApi\Undefined;

/**
 * Describes a single HTTP header.
 *
 * @see [Header Object](https://spec.openapis.org/oas/v3.1.1.html#header-object)
 */
#[\Attribute(\Attribute::IS_REPEATABLE)]
class Header extends AbstractAttribute
{
    public ?string $style = null;

    /** @var list<MediaType>|null */
    public ?array $content = null;

    /**
     * @param string|null                    $header      The header name (component key)
     * @param string|null                    $description A brief description of the header (CommonMark syntax)
     * @param bool|null                      $required    Whether the header is mandatory
     * @param bool|null                      $deprecated  Whether the header is deprecated
     * @param string|null                    $ref         A JSON Reference to a reusable header
     * @param string|ParameterStyle|null     $style       How the header value is serialized
     * @param bool|null                      $explode     Whether arrays/objects generate separate parameters
     * @param Schema|null                    $schema      The schema defining the type for the header
     * @param mixed                          $example     Example of the header's value
     * @param list<Example>|null             $examples    Examples of the header's value
     * @param MediaType|list<MediaType>|null $content     Content-type based header serialization
     * @param array<string,mixed>|null       $x           Vendor extensions (x-* properties)
     * @param list<Attachable>|null          $attachables Reusable custom attachable attributes
     */
    public function __construct(
        public ?string $header = null,
        public ?string $description = null,
        public ?bool $required = null,
        public ?bool $deprecated = null,
        public ?string $ref = null,
        string|ParameterStyle|null $style = null,
        public ?bool $explode = null,
        public ?Schema $schema = null,
        public mixed $example = Undefined::UNDEFINED,
        public ?array $examples = null,
        MediaType|array|null $content = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct(x: $x, attachables: $attachables);
        $this->style = $style instanceof \BackedEnum ? $style->value : $style;
        $this->content = self::wrapList($content);
    }

    public function merge(): array
    {
        return [
            Components::class => 'headers[]',
            Response::class => 'headers[]',
            Encoding::class => 'headers[]',
        ];
    }

    public function contains(): array
    {
        return [
            MediaType::class => 'content[]',
            Example::class => 'examples[]',
        ];
    }
}
