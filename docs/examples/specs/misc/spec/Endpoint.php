<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Misc\Spec;

use OpenApi\Spec as OA;

/**
 * An API endpoint.
 */
class Endpoint
{
    /**
     * An API endpoint.
     */
    #[OA\Operation\Get(
        path: '/api/endpoint',
        operationId: 'endpoint',
        description: 'An endpoint',
        tags: ['endpoints'],
        parameters: [
            new OA\Parameter(
                name: 'filter',
                in: 'query',
                content: [new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(properties: [
                        new OA\Property(property: 'type', schema: new OA\Schema(type: 'string')),
                        new OA\Property(property: 'color', schema: new OA\Schema(type: 'string')),
                    ]),
                )],
            ),
        ],
        responses: [
            new OA\Response(response: 200, ref: '#/components/responses/200'),
        ],
        security: [new OA\Security\Requirement(scheme: 'bearerAuth', scopes: [])],
    )]
    public function endpoint()
    {
    }
}
