<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'YoYo',
)]
class ExampleSchema
{
}

#[OAT\Info(title: 'Examples', version: '1.0')]
#[OAT\Get(
    path: '/endpoint/{name}/{other}',
    parameters: [
        new OAT\QueryParameter(
            name: 'name',
            example: 'Fritz'
        ),
        new OAT\QueryParameter(
            name: 'other',
            examples: [
                new OAT\Examples(
                    example: 'o1',
                    summary: 'other example 1',
                    value: 'ping'
                ),
                new OAT\Examples(
                    example: 'o2',
                    summary: 'other example 2',
                    value: 'pong'
                ),
            ]
        ),
    ],
    responses: [
        new OAT\Response(response: 200, description: 'OK'),
    ]
)]
class ExamplesEndpoint
{
}
