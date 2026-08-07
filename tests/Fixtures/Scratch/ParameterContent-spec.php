<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Fixtures\Scratch;

use OpenApi\Spec as OA;

/**
 * An API endpoint.
 */
#[OA\Info(
    title: 'Parameter Content Scratch',
    version: '1.0'
)]
#[OA\Operation\Get(
    path: '/api/endpoint',
    tags: ['endpoints'],
    description: 'An endpoint',
    operationId: 'endpoint',
    parameters: [
        new OA\Parameter\Query(
            name: 'filter',
            content: new OA\MediaType\Json(
                properties: [
                    new OA\Property(property: 'type', schema: new OA\Schema(type: 'string')),
                    new OA\Property(property: 'color', schema: new OA\Schema(type: 'string')),
                ]
            )
        ),
    ],
    responses: [new OA\Response(response: 200, description: 'OK')]
)]
class ParameterContentEndpointSpec
{
}
