<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Operation;

use OpenApi\Spec as OA;

/**
 * Shorthand for an HTTP POST operation.
 *
 * @see [Operation Object](https://spec.openapis.org/oas/v3.1.1.html#operation-object)
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Post extends OA\Operation
{
    /**
     * @param list<string>|null        $tags
     * @param list<OA\Parameter>|null  $parameters
     * @param list<OA\Response>|null   $responses
     * @param array<string,mixed>|null $callbacks
     * @param list<array>|null         $security
     * @param list<OA\Server>|null     $servers
     * @param array<string,mixed>|null $x
     */
    public function __construct(
        ?string $path = null,
        ?string $operationId = null,
        ?string $summary = null,
        ?string $description = null,
        ?array $tags = null,
        ?array $parameters = null,
        ?OA\RequestBody $requestBody = null,
        ?array $responses = null,
        ?array $callbacks = null,
        ?bool $deprecated = null,
        ?array $security = null,
        ?array $servers = null,
        ?OA\ExternalDocumentation $externalDocs = null,
        ?array $x = null,
    ) {
        parent::__construct(
            path: $path,
            method: 'post',
            operationId: $operationId,
            summary: $summary,
            description: $description,
            tags: $tags,
            parameters: $parameters,
            requestBody: $requestBody,
            responses: $responses,
            callbacks: $callbacks,
            deprecated: $deprecated,
            security: $security,
            servers: $servers,
            externalDocs: $externalDocs,
            x: $x,
        );
    }
}
