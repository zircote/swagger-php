<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Operation;

use OpenApi\Spec;

/**
 * Shorthand for an HTTP HEAD operation.
 *
 * @see [Operation Object](https://spec.openapis.org/oas/v3.1.1.html#operation-object)
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Head extends Spec\Operation
{
    /**
     * @param list<string>|null         $tags
     * @param list<Spec\Parameter>|null $parameters
     * @param list<Spec\Response>|null  $responses
     * @param array<string,mixed>|null  $callbacks
     * @param list<array>|null          $security
     * @param list<Spec\Server>|null    $servers
     * @param array<string,mixed>|null  $x
     */
    public function __construct(
        ?string $path = null,
        ?string $operationId = null,
        ?string $summary = null,
        ?string $description = null,
        ?array $tags = null,
        ?array $parameters = null,
        ?Spec\RequestBody $requestBody = null,
        ?array $responses = null,
        ?array $callbacks = null,
        ?bool $deprecated = null,
        ?array $security = null,
        ?array $servers = null,
        ?Spec\ExternalDocumentation $externalDocs = null,
        ?array $x = null,
    ) {
        parent::__construct(
            path: $path,
            method: 'head',
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
