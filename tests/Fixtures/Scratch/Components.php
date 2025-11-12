<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Components(
    schemas: [
        new OAT\Schema(
            schema: 'first schema',
        ),
    ],
    requestBodies: [
        new OAT\RequestBody(request: 'first request body'),
    ]
)]
class ComponentsClass1
{
}

#[OAT\Components(
    schemas: [
        new OAT\Schema(
            schema: 'second schema',
        ),
    ],
)]
class ComponentsClass2
{
}

#[OAT\Info(title: 'Components', version: '1.0')]
#[OAT\Get(
    path: '/endpoint',
    responses: [
        new OAT\Response(
            response: 200,
            description: 'All good',
        ),
    ]
)]
class ComponentsEndpoint
{
}
