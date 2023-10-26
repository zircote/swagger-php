<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Info(
    title: 'Multiple Paths For Endpoint Scratch',
    version: '1.0'
)]
#[OAT\Get(
    path: '/api/class/endpoint',
    description: 'A class endpoint',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
#[OAT\Get(
    path: '/api/class/endpoint2',
    description: 'Another class endpoint',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
class MultiplePathsForClassEndpoint
{
}

class MultiplePathsForMethodEndpoint
{
    #[OAT\Get(
        path: '/api/method/endpoint',
        description: 'A method endpoint',
        responses: [new OAT\Response(response: 200, description: 'OK')]
    )]
    #[OAT\Get(
        path: '/api/method/endpoint2',
        description: 'Another method endpoint',
        responses: [new OAT\Response(response: 200, description: 'OK')]
    )]
    public function endpoint()
    {
    }
}
