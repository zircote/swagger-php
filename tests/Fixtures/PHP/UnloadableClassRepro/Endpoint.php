<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP\UnloadableClassRepro;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'UnloadableClassRepro', version: '1.0')]
#[OAT\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'getEndpoint',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
class Endpoint
{
}
