<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Misc\Spec;

use OpenApi\Spec as OA;

class UserUpdateEndpoint
{
    #[OA\Operation\Put(
        path: '/users/{id}',
        operationId: 'updateUser',
        summary: 'Updates a user',
        description: 'Updates a user',
        tags: ['user'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'Parameter with mutliple examples',
                required: true,
                schema: new OA\Schema(type: 'string'),
                examples: [
                    new OA\Example(example: 'int', summary: 'An int value.', value: '1'),
                    new OA\Example(example: 'uuid', summary: 'An UUID value.', value: '0006faf6-7a61-426c-9034-579f2cfcfa83'),
                ],
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
        ],
    )]
    public function updateUser()
    {
    }
}
