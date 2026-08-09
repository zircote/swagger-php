<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Operation;

use OpenApi\Spec as OA;
use OpenApi\Spec\Parameter;
use OpenApi\Spec\RequestBody;
use OpenApi\Spec\Response;
use OpenApi\Spec\Server;
use OpenApi\Undefined;

/**
 * Shorthand for an HTTP GET operation.
 *
 * @see [Operation Object](https://spec.openapis.org/oas/v3.1.1.html#operation-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Get extends OA\Operation
{
    /**
     * @param list<string>|null                  $tags
     * @param list<Parameter>|null               $parameters
     * @param list<Response>|null                $responses
     * @param array<string,mixed>|null           $callbacks
     * @param list<OA\Security\Requirement>|null $security
     * @param list<Server>|null                  $servers
     * @param array<string,mixed>|null           $x
     * @param list<OA\Attachable>|null           $attachables
     */
    public function __construct(
        ?string $path = null,
        ?string $webhook = null,
        ?string $operationId = null,
        ?string $summary = Undefined::UNDEFINED,
        ?string $description = Undefined::UNDEFINED,
        ?array $tags = null,
        ?array $parameters = null,
        ?array $responses = null,
        ?array $callbacks = null,
        ?bool $deprecated = null,
        ?array $security = null,
        ?array $servers = null,
        ?OA\ExternalDocumentation $externalDocs = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct(
            path: $path,
            webhook: $webhook,
            method: OA\HttpMethod::Get,
            operationId: $operationId,
            summary: $summary,
            description: $description,
            tags: $tags,
            parameters: $parameters,
            responses: $responses,
            callbacks: $callbacks,
            deprecated: $deprecated,
            security: $security,
            servers: $servers,
            externalDocs: $externalDocs,
            x: $x,
            attachables: $attachables,
        );
    }

    public function contains(): array
    {
        $result = parent::contains();
        unset($result[RequestBody::class]);

        return $result;
    }
}
