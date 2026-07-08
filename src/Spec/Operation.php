<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Operation extends AbstractAttribute
{
    /**
     * @param list<string>|null        $tags
     * @param list<Parameter>|null     $parameters
     * @param list<Response>|null      $responses
     * @param array<string,mixed>|null $callbacks
     * @param list<array>|null         $security
     * @param list<Server>|null        $servers
     * @param array<string,mixed>|null $x
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
        return [Parameter::class, Response::class, RequestBody::class, Server::class];
    }
}
