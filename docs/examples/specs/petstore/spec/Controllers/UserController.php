<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Spec\Controllers;

use OpenApi\Examples\Specs\Petstore\Spec\Models\User;
use OpenApi\Examples\Specs\Petstore\Spec\Models\UserArrayRequestBody;
use OpenApi\Spec as OA;

/**
 * Class User.
 */
class UserController
{
    #[OA\Operation\Post(
        path: '/user',
        operationId: 'createUser',
        summary: 'Create user',
        description: 'This can only be done by the logged in user.',
        tags: ['user'],
        requestBody: new OA\RequestBody(
            description: 'Create user object',
            required: true,
            content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: User::class))],
        ),
        responses: [
            new OA\Response(response: 'default', description: 'successful operation'),
        ],
    )]
    public function createUser()
    {
    }

    #[OA\Operation\Post(
        path: '/user/createWithArray',
        operationId: 'createUsersWithListInput',
        summary: 'Create list of users with given input array',
        tags: ['user'],
        requestBody: new OA\RequestBody(ref: UserArrayRequestBody::class),
        responses: [
            new OA\Response(response: 'default', description: 'successful operation'),
        ],
    )]
    public function createUsersWithListInput()
    {
    }

    #[OA\Operation\Get(
        path: '/user/login',
        operationId: 'loginUser',
        summary: 'Logs user into system',
        tags: ['user'],
        parameters: [
            new OA\Parameter(name: 'username', in: 'query', description: 'The user name for login', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'password', in: 'query', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'successful operation',
                headers: [
                    new OA\Header(header: 'X-Rate-Limit', description: 'calls per hour allowed by the user', schema: new OA\Schema(type: 'integer', format: 'int32')),
                    new OA\Header(header: 'X-Expires-After', description: 'date in UTC when token expires', schema: new OA\Schema(type: 'string', format: 'datetime')),
                ],
                content: [
                    new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(type: 'string')),
                    new OA\MediaType(mediaType: 'application/xml', schema: new OA\Schema(type: 'string')),
                ],
            ),
            new OA\Response(response: 400, description: 'Invalid username/password supplied'),
        ],
    )]
    public function loginUser()
    {
    }

    #[OA\Operation\Get(
        path: '/user/logout',
        operationId: 'logoutUser',
        summary: 'Logs out current logged in user session',
        tags: ['user'],
        responses: [
            new OA\Response(response: 'default', description: 'successful operation'),
        ],
    )]
    public function logoutUser()
    {
    }

    #[OA\Operation\Get(
        path: '/user/{username}',
        operationId: 'getUserByName',
        summary: 'Get user by user name',
        parameters: [
            new OA\Parameter(name: 'username', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'successful operation',
                content: [
                    new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: User::class)),
                    new OA\MediaType(mediaType: 'application/xml', schema: new OA\Schema(ref: User::class)),
                ],
            ),
            new OA\Response(response: 400, description: 'Invalid username supplied'),
            new OA\Response(response: 404, description: 'User not found'),
        ],
    )]
    public function getUserByName()
    {
    }

    #[OA\Operation\Put(
        path: '/user/{username}',
        operationId: 'updateUser',
        summary: 'Update user',
        description: 'This can only be done by the logged in user.',
        parameters: [
            new OA\Parameter(name: 'username', in: 'path', description: 'name that to be updated', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            description: 'Updated user object',
            required: true,
            content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: User::class))],
        ),
        responses: [
            new OA\Response(response: 400, description: 'Invalid user supplied'),
            new OA\Response(response: 404, description: 'User not found'),
        ],
    )]
    public function updateUser()
    {
    }

    #[OA\Operation\Delete(
        path: '/user/{username}',
        operationId: 'deleteUser',
        summary: 'Delete user',
        description: 'This can only be done by the logged in user.',
        parameters: [
            new OA\Parameter(name: 'username', in: 'path', description: 'The name that needs to be deleted', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 400, description: 'Invalid username supplied'),
            new OA\Response(response: 404, description: 'User not found'),
        ],
    )]
    public function deleteUser()
    {
    }
}
