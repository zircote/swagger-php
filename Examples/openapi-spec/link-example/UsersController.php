<?php

namespace OpenApi\LinkExample;

/**
 * MVC controller that handles "users/*" urls.
 */
class UsersController
{

    /**
     * @SWG\Get(path="/2.0/users/{username}",
     *   operationId="getUserByName",
     *   @SWG\Parameter(name="username",
     *     in="path",
     *     required=true,
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Response(response="200",
     *     description="The User",
     *     @SWG\MediaType(mediaType="application/json",
     *       @SWG\Schema(ref="#/components/schemas/user")
     *     ),
     *     @SWG\Link(link="userRepositories", ref="#/components/links/UserRepositories")
     *   )
     * )
     */
    public function getUserByName($username)
    {
    }
}
