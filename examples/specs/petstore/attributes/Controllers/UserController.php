<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Attributes\Controllers;

use OpenApi\Attributes as OAT;
use OpenApi\Examples\Specs\Petstore\Attributes\Models\User;
use OpenApi\Examples\Specs\Petstore\Attributes\Models\UserArrayRequestBody;

/**
 * Class User.
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class UserController
{
    #[OAT\Post(
        path: '/user',
        tags: ['user'],
        summary: 'Create user',
        description: 'This can only be done by the logged in user.',
        operationId: 'createUser',
        responses: [
            new OAT\Response(
                response: 'default',
                description: 'successful operation'
            ),
        ],
        requestBody: new OAT\RequestBody(
            description: 'Create user object',
            required: true,
            content: new OAT\JsonContent(
                ref: User::class
            )
        )
    )]
    public function createUser()
    {
    }

    #[OAT\Post(
        path: '/user/createWithArray',
        tags: ['user'],
        summary: 'Create list of users with given input array',
        operationId: 'createUsersWithListInput',
        responses: [
            new OAT\Response(
                response: 'default',
                description: 'successful operation'
            ),
        ],
        requestBody: new OAT\RequestBody(
            ref: UserArrayRequestBody::class
        )
    )]
    public function createUsersWithListInput()
    {
    }

    #[OAT\Get(
        path: '/user/login',
        tags: ['user'],
        summary: 'Logs user into system',
        operationId: 'loginUser',
        parameters: [
            new OAT\Parameter(
                name: 'username',
                in: 'query',
                description: 'The user name for login',
                required: true,
                schema: new OAT\Schema(
                    type: 'string'
                )
            ),
            new OAT\Parameter(
                name: 'password',
                in: 'query',
                required: true,
                schema: new OAT\Schema(
                    type: 'string'
                )
            ),
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'successful operation',
                headers: [
                    new OAT\Header(
                        header: 'X-Rate-Limit',
                        description: 'calls per hour allowed by the user',
                        schema: new OAT\Schema(
                            type: 'integer',
                            format: 'int32'
                        )
                    ),
                    new OAT\Header(
                        header: 'X-Expires-After',
                        description: 'date in UTC when token expires',
                        schema: new OAT\Schema(
                            type: 'string',
                            format: 'datetime'
                        )
                    ),
                ],
                content: [
                    new OAT\JsonContent(
                        type: 'string'
                    ),
                    new OAT\XmlContent(
                        type: 'string'
                    ),
                ]
            ),
            new OAT\Response(
                response: 400,
                description: 'Invalid username/password supplied'
            ),
        ]
    )]
    public function loginUser()
    {
    }

    #[OAT\Get(
        path: '/user/logout',
        tags: ['user'],
        summary: 'Logs out current logged in user session',
        operationId: 'logoutUser',
        responses: [
            new OAT\Response(
                response: 'default',
                description: 'successful operation'
            ),
        ]
    )]
    public function logoutUser()
    {
    }

    #[OAT\Get(
        path: '/user/{username}',
        summary: 'Get user by user name',
        operationId: 'getUserByName',
        parameters: [
            new OAT\PathParameter(
                name: 'username',
                required: true,
                schema: new OAT\Schema(
                    type: 'string'
                )
            ),
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'successful operation',
                content: [
                    new OAT\JsonContent(
                        ref: User::class
                    ),
                    new OAT\XmlContent(
                        ref: User::class
                    ),
                ]
            ),
            new OAT\Response(
                response: 400,
                description: 'Invalid username supplied'
            ),
            new OAT\Response(
                response: 404,
                description: 'User not found'
            ),
        ]
    )]
    public function getUserByName()
    {
    }

    #[OAT\Put(
        path: '/user/{username}',
        summary: 'Update user',
        description: 'This can only be done by the logged in user.',
        operationId: 'updateUser',
        parameters: [
            new OAT\PathParameter(
                name: 'username',
                required: true,
                description: 'name that to be updated',
                schema: new OAT\Schema(
                    type: 'string'
                )
            ),
        ],
        responses: [
            new OAT\Response(
                response: 400,
                description: 'Invalid user supplied'
            ),
            new OAT\Response(
                response: 404,
                description: 'User not found'
            ),
        ],
        requestBody: new OAT\RequestBody(
            description: 'Updated user object',
            required: true,
            content: new OAT\JsonContent(
                ref: User::class
            )
        )
    )]
    public function updateUser()
    {
    }

    #[OAT\Delete(
        path: '/user/{username}',
        summary: 'Delete user',
        description: 'This can only be done by the logged in user.',
        operationId: 'deleteUser',
        parameters: [
            new OAT\Parameter(
                name: 'username',
                in: 'path',
                description: 'The name that needs to be deleted',
                required: true,
                schema: new OAT\Schema(
                    type: 'string'
                )
            ),
        ],
        responses: [
            new OAT\Response(
                response: 400,
                description: 'Invalid username supplied'
            ),
            new OAT\Response(
                response: 404,
                description: 'User not found'
            ),
        ]
    )]
    public function deleteUser()
    {
    }
}
