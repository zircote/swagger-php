<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Misc\Spec;

use OpenApi\Spec as OA;

class UserAddEndpoint
{
    #[OA\Operation\Post(
        path: '/users',
        operationId: 'addUser',
        summary: 'Adds a new user - with oneOf examples',
        description: 'Adds a new user',
        tags: ['user'],
        requestBody: new OA\RequestBody(
            content: [new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'id', schema: new OA\Schema(type: 'string')),
                        new OA\Property(property: 'name', schema: new OA\Schema(type: 'string')),
                        new OA\Property(property: 'phone', schema: new OA\Schema(oneOf: [
                            new OA\Schema(type: 'string'),
                            new OA\Schema(type: 'integer'),
                        ])),
                    ],
                    example: ['id' => 'a3fb6', 'name' => 'Jessica Smith', 'phone' => 12345678],
                ),
            )],
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: [new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(oneOf: [
                        new OA\Schema(ref: '#/components/schemas/Result'),
                        new OA\Schema(type: 'boolean'),
                    ]),
                    examples: [
                        new OA\Example(example: 'result', summary: 'An result object.', value: ['success' => true]),
                        new OA\Example(example: 'bool', summary: 'A boolean value.', value: false),
                    ],
                )],
            ),
        ],
    )]
    public function addUser()
    {
    }
}
