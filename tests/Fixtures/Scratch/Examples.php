<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

// `Schema::$examples` is the JSON Schema keyword: a list of values, so `example` and `summary`
// have nowhere to go and are not asked for.
#[OAT\Schema(
    schema: 'YoYo',
    examples: [
        new OAT\Examples(
            value: 'YoYo'
        ),
    ]
)]
class ExampleSchema
{
}

#[OAT\Info(title: 'Examples', version: '1.0')]
#[OAT\Get(
    path: '/endpoint/{name}/{other}',
    operationId: 'examples',
    parameters: [
        new OAT\PathParameter(
            name: 'name',
            required: true,
            schema: new OAT\Schema(type: 'string'),
            example: 'Fritz'
        ),
        new OAT\PathParameter(
            name: 'other',
            required: true,
            schema: new OAT\Schema(type: 'string'),
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
