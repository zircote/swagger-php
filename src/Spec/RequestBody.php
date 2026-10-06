<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Describes a single request body.
 *
 * @see [Request Body Object](https://spec.openapis.org/oas/v3.1.1.html#request-body-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class RequestBody extends AbstractAttribute
{
    /** The key this attribute is filed under in `components`; null for an inline one. */
    public ?string $component = null;

    /** @var list<MediaType>|null */
    public ?array $content = null;

    /**
     * @param string|null                    $request     Deprecated since 6.12, removed in 8.0 - use `component` instead
     * @param string|null                    $description A brief description of the request body (CommonMark syntax)
     * @param bool|null                      $required    Whether the request body is required
     * @param string|Schema\Ref|null         $ref         A JSON Reference to a reusable request body
     * @param MediaType|list<MediaType>|null $content     The content of the request body
     * @param string|null                    $component   The key this is filed under in `components`, which makes it a reusable component
     * @param array<string,mixed>|null       $x           Vendor extensions (x-* properties)
     * @param list<Attachable>|null          $attachables Reusable custom attachable attributes
     */
    public function __construct(
        public ?string $request = null,
        public ?string $description = null,
        public ?bool $required = null,
        public string|Schema\Ref|null $ref = null,
        MediaType|array|null $content = null,
        ?string $component = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct(x: $x, attachables: $attachables);
        $this->component = $component;
        if ($component === null && $this->request !== null) {
            trigger_deprecation('zircote/swagger-php', '6.12', '`request` is deprecated as the component key of %s and will be removed in 8.0; use `component`', static::class);
            $this->component = $this->request;
        }
        $this->content = self::wrapList($content);
    }

    public function isRoot(): bool
    {
        return $this->component !== null;
    }

    public function merge(): array
    {
        return [
            Components::class => 'requestBodies[]',
            Operation::class => 'requestBody',
        ];
    }

    public function contained(): array
    {
        return [
            Operation::class => 'requestBody',
        ];
    }
}
