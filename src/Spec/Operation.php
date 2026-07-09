<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Describes a single API operation on a path.
 *
 * @see [Operation Object](https://spec.openapis.org/oas/v3.1.1.html#operation-object)
 * @see [Path Item Object](https://spec.openapis.org/oas/v3.1.1.html#path-item-object)
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Operation extends AbstractAttribute
{
    /**
     * @param string|null                $path         The URL path for the operation
     * @param string|null                $webhook      The webhook name (mutually exclusive with path)
     * @param string|null                $method       The HTTP method (get, post, put, delete, etc.)
     * @param string|null                $operationId  Unique identifier for the operation
     * @param string|null                $summary      A short summary of what the operation does
     * @param string|null                $description  A verbose explanation of the operation (CommonMark syntax)
     * @param list<string>|null          $tags         Tags for API documentation grouping
     * @param list<Parameter>|null       $parameters   Parameters applicable to this operation
     * @param RequestBody|null           $requestBody  The request body applicable to this operation
     * @param list<Response>|null        $responses    The list of possible responses
     * @param array<string,mixed>|null   $callbacks    Possible out-of-band callbacks related to the operation
     * @param bool|null                  $deprecated   Whether the operation is deprecated
     * @param list<array>|null           $security     Security mechanisms that can be used for this operation
     * @param list<Server>|null          $servers      Alternative servers for this operation
     * @param ExternalDocumentation|null $externalDocs Additional external documentation
     * @param array<string,mixed>|null   $x            Vendor extensions (x-* properties)
     */
    public function __construct(
        public ?string $path = null,
        public ?string $webhook = null,
        public ?string $method = null,
        public ?string $operationId = null,
        public ?string $summary = null,
        public ?string $description = null,
        public ?array $tags = null,
        public ?array $parameters = null,
        public ?RequestBody $requestBody = null,
        public ?array $responses = null,
        public ?array $callbacks = null,
        public ?bool $deprecated = null,
        public ?array $security = null,
        public ?array $servers = null,
        public ?ExternalDocumentation $externalDocs = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function isRoot(): bool
    {
        return true;
    }

    public function contains(): array
    {
        return [
            Parameter::class => 'parameters[]',
            Response::class => 'responses[]',
            RequestBody::class => 'requestBody',
            Server::class => 'servers[]',
        ];
    }
}
