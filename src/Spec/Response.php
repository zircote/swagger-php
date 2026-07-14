<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Describes a single response from an API operation.
 *
 * @see [Response Object](https://spec.openapis.org/oas/v3.1.1.html#response-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Response extends AbstractAttribute
{
    /**
     * @param string|int|null          $response    The HTTP status code or 'default'
     * @param string|null              $description A description of the response (CommonMark syntax)
     * @param string|null              $ref         A JSON Reference to a reusable response
     * @param list<Header>|null        $headers     Headers sent with the response
     * @param list<MediaType>|null     $content     Possible response payloads
     * @param list<Link>|null          $links       Design-time links for the response
     * @param array<string,mixed>|null $x           Vendor extensions (x-* properties)
     * @param list<Attachable>|null    $attachables Reusable custom attachable attributes
     */
    public function __construct(
        public string|int|null $response = null,
        public ?string $description = null,
        public ?string $ref = null,
        public ?array $headers = null,
        public ?array $content = null,
        public ?array $links = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct(x: $x, attachables: $attachables);
    }

    public function isRoot(): bool
    {
        return $this->ref === null && $this->response !== null;
    }

    public function merge(): array
    {
        return [Operation::class => 'responses[]', PathItem::class => 'responses[]'];
    }

    public function contains(): array
    {
        return [
            Header::class => 'headers[]',
            MediaType::class => 'content[]',
            Link::class => 'links[]',
        ];
    }
}
