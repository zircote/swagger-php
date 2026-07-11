<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingLinks\Spec;

use OpenApi\Spec as OA;

#[OA\Tag(name: 'Users', description: 'Users')]
class UsersController
{
    #[OA\Operation\Get(
        path: '/2.0/users/{username}',
        operationId: 'getUserByName',
        summary: 'Get user details by username',
        tags: ['Users'],
        parameters: [
            new OA\Parameter(name: 'username', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'The User',
                content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: '#/components/schemas/user'))],
                links: [new OA\Link(link: 'userRepositories', ref: '#/components/links/UserRepositories')],
            ),
        ],
    )]
    public function getUserByName(string $username)
    {
    }
}
