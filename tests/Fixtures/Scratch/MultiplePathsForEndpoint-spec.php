<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Info(
    title: 'Multiple Paths For Endpoint Scratch',
    version: '1.0'
)]
#[OA\Operation\Get(
    path: '/api/class/endpoint',
    description: 'A class endpoint',
    operationId: 'getMultiple',
    responses: [new OA\Response(response: 200, description: 'OK')]
)]
#[OA\Operation\Get(
    path: '/api/class/endpoint2',
    description: 'Another class endpoint',
    operationId: 'getMultipleAnother',
    responses: [new OA\Response(response: 200, description: 'OK')]
)]
class MultiplePathsForClassEndpointSpec
{
}

class MultiplePathsForMethodEndpointSpec
{
    #[OA\Operation\Get(
        path: '/api/method/endpoint',
        description: 'A method endpoint',
        operationId: 'methodEndpoint',
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    #[OA\Operation\Get(
        path: '/api/method/endpoint2',
        description: 'Another method endpoint',
        operationId: 'anotherMethodEndpoint',
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function endpoint()
    {
    }
}
