<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Components(
    schemas: [
        new OA\Schema(
            schema: 'first schema',
        ),
    ],
    requestBodies: [
        new OA\RequestBody(request: 'first request body'),
    ]
)]
class ComponentsClass1Spec
{
}

#[OA\Components(
    schemas: [
        new OA\Schema(
            schema: 'second schema',
        ),
    ],
)]
class ComponentsClass2Spec
{
}

#[OA\Info(title: 'Components', version: '1.0')]
#[OA\Operation\Get(
    path: '/endpoint',
    operationId: 'getEndpoint',
)]
#[OA\Response(
    response: 200,
    description: 'All good',
)]
class ComponentsEndpointSpec
{
}
