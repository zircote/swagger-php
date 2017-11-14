<?php

namespace OpenApi\LinkExample;

/**
 * MVC controller that handles "users/*" urls.
 */
class UsersController
{

    /**
     * @OAS\Get(path="/2.0/users/{username}",
     *   operationId="getUserByName",
     *   @OAS\Parameter(name="username",
     *     in="path",
     *     required=true,
     *     @OAS\Schema(type="string")
     *   ),
     *   @OAS\Response(response="200",
     *     description="The User",
     *     @OAS\MediaType(mediaType="application/json",
     *       @OAS\Schema(ref="#/components/schemas/user")
     *     ),
     *     @OAS\Link(link="userRepositories", ref="#/components/links/UserRepositories")
     *   )
     * )
     */
    public function getUserByName($username)
    {
    }

    /**
     * @OAS\Post(path="/2.0/user",
     *   operationId="createUser",
     *   requestBody=@OAS\RequestBody(
     *     description="swäggins",
     *     required=true,
     *     @OAS\MediaType(mediaType="application/json",
     *       @OAS\Schema(ref="#/components/schemas/user")
     *     ),
     *     @OAS\MediaType(mediaType="application/xml",
     *       @OAS\Schema(ref="#/components/schemas/user")
     *     ),
     *   ),
     *   @OAS\Response(response="200",
     *     description="The User",
     *     @OAS\MediaType(mediaType="application/json",
     *       @OAS\Schema(ref="#/components/schemas/user")
     *     ),
     *     @OAS\Link(link="userRepositories", ref="#/components/links/UserRepositories")
     *   )
     * )
     */
    public function createUser()
    {
    }
}
