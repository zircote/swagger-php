<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(
    schema: 'YoYo',
    examples: [
        new OA\Example(
            example: 'yo',
            summary: 'the yo',
            value: 'YoYo'
        ),
    ]
)]
class ExampleSchemaSpec
{
}

#[OA\Info(title: 'Examples', version: '1.0')]
#[OA\Operation\Get(
    path: '/endpoint/{name}/{other}',
    operationId: 'examples',
    parameters: [
        new OA\Parameter\Path(
            name: 'name',
            required: true,
            schema: new OA\Schema(type: 'string'),
            example: 'Fritz'
        ),
        new OA\Parameter\Path(
            name: 'other',
            required: true,
            schema: new OA\Schema(type: 'string'),
            examples: [
                new OA\Example(
                    example: 'o1',
                    summary: 'other example 1',
                    value: 'ping'
                ),
                new OA\Example(
                    example: 'o2',
                    summary: 'other example 2',
                    value: 'pong'
                ),
            ]
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'OK'),
    ]
)]
class ExamplesEndpointSpec
{
}
