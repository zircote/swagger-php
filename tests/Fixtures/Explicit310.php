<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\OpenApi(
    openapi: OAT\OpenApi::VERSION_3_1_0,
    info: new OAT\Info(
        version: '0.0.1',
        title: 'Foo API',
    ),
)]
#[OAT\Get(
    path: '/foo',
    operationId: 'fooOperation',
    description: 'Retrieve details for a foo',
    summary: 'Foo Details',
    tags: ['Foo'],
    responses: [
        new OAT\Response(
            response: 200,
            description: 'Successful Operation',
            content: new OAT\JsonContent(
                properties: [
                    new OAT\Property(
                        property: 'bar',
                        type: 'string',
                    ),
                ],
            ),
        ),
    ],
)]
class Explicit310 { }
