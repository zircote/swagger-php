<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\RequestBody]
class Request
{
    #[OAT\Post(
        path: '/',
        requestBody: new OAT\RequestBody(ref: Request::class),
        responses: [new OAT\Response(response: 200, description: 'An example resource')],
    )]
    public function post()
    {

    }
}
