<?php

namespace OpenApi\Examples\OpenapiSpecAttributes;

use OpenApi\Annotations as OA;

/**
 * MVC controller that handles "users/*" urls.
 */
class UsersController
{

    /**
     * @OA\Get(path="/2.0/users/{username}",
     *   operationId="getUserByName",
     *   @OA\Parameter(name="username",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(response="200",
     *     description="The User",
     *     @OA\JsonContent(ref="#/components/schemas/user"),
     *     @OA\Link(link="userRepositories", ref="#/components/links/UserRepositories")
     *   )
     * )
     */
    #[OA\Get(path: '/2.0/users/{username}', operationId: 'getUserByName', parameters: [new OA\Parameter(name: 'username', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'The User', content: new OA\JsonContent(ref: '#/components/schemas/user'), links: [new OA\Link(link: 'userRepositories', ref: '#/components/links/UserRepositories')])])]
    public function getUserByName($username)
    {
    }
}
