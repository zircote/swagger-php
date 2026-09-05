<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Misc\Spec;

use OpenApi\Spec as OA;

/**
 * Another API endpoint.
 */
class MultiValueQueryParamEndpoint
{
    /**
     * Another API endpoint.
     */
    #[OA\Operation\Get(
        path: '/api/anotherendpoint',
        operationId: 'anotherendpoints',
        description: 'Another endpoint',
        tags: ['endpoints'],
        parameters: [
            new OA\Parameter(
                name: 'things[]',
                in: OA\ParameterIn::Query,
                description: 'A list of things.',
                required: false,
                schema: new OA\Schema(type: 'array', items: new OA\Schema(type: 'integer')),
            ),
        ],
        security: [new OA\Security\Requirement(scheme: 'bearerAuth', scopes: [])],
    )]
    #[OA\Response(response: 200, ref: '#/components/responses/200')]
    public function anotherEndpoint()
    {
    }
}
