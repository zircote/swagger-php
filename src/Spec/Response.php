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
     * The shape of `$response` when the response is nested in an operation: a status code,
     * a range, or `default`. In the `responses` bucket the same field is a component name
     * instead, and matching this there means the response was meant to nest and did not.
     */
    public const STATUS_CODE_PATTERN = '/^(default|[1-5][0-9]{2}|[1-5]XX)$/';

    /** The key this attribute is filed under in `components`; null for an inline one. */
    public ?string $component = null;

    /** @var list<MediaType>|null */
    public ?array $content = null;

    /**
     * @param string|int|null                $response    The HTTP status code, a range such as '2XX', or 'default' — the key the response nests under in an operation
     * @param string|null                    $description A description of the response (CommonMark syntax)
     * @param string|Schema\Ref|null         $ref         A JSON Reference to a reusable response
     * @param list<Header>|null              $headers     Headers sent with the response
     * @param MediaType|list<MediaType>|null $content     Possible response payloads
     * @param list<Link>|null                $links       Design-time links for the response
     * @param string|null                    $component   The key this is filed under in `components`, which makes it a reusable component
     * @param array<string,mixed>|null       $x           Vendor extensions (x-* properties)
     * @param list<Attachable>|null          $attachables Reusable custom attachable attributes
     */
    public function __construct(
        public string|int|null $response = null,
        public ?string $description = null,
        public string|Schema\Ref|null $ref = null,
        public ?array $headers = null,
        MediaType|array|null $content = null,
        public ?array $links = null,
        ?string $component = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct(x: $x, attachables: $attachables);
        $this->component = $component;
        $this->content = self::wrapList($content);
    }

    public function isRoot(): bool
    {
        return $this->ref === null && ($this->component !== null || $this->response !== null);
    }

    public function merge(): array
    {
        return [
            Components::class => 'responses[]',
            Operation::class => 'responses[]',
            PathItem::class => 'responses[]',
        ];
    }

    public function contained(): array
    {
        return [
            Operation::class => 'responses[]',
            PathItem::class => 'responses[]',
        ];
    }
}
