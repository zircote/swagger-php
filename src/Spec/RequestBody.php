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
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class RequestBody extends AbstractAttribute
{
    /**
     * @param string|null              $request     Reusable request body identifier (component key)
     * @param string|null              $description A brief description of the request body (CommonMark syntax)
     * @param bool|null                $required    Whether the request body is required
     * @param string|null              $ref         A JSON Reference to a reusable request body
     * @param list<MediaType>|null     $content     The content of the request body
     * @param array<string,mixed>|null $x           Vendor extensions (x-* properties)
     */
    public function __construct(
        public ?string $request = null,
        public ?string $description = null,
        public ?bool $required = null,
        public ?string $ref = null,
        public ?array $content = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function merge(): array
    {
        return [Operation::class => 'requestBody'];
    }

    public function contains(): array
    {
        return [MediaType::class => 'content[]'];
    }
}
