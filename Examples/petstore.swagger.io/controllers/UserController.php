<?php

namespace PetstoreIO;

class UserController
{

    /**
     * @OAS\Post(path="/user",
     *   tags={"user"},
     *   summary="Create user",
     *   description="This can only be done by the logged in user.",
     *   operationId="createUser",
     *   @OAS\Parameter(
     *     in="body",
     *     name="body",
     *     description="Created user object",
     *     required=true,
     *     @OAS\Schema(ref="#/definitions/User")
     *   ),
     *   @OAS\Response(response="default", description="successful operation")
     * )
     */
    public function createUser()
    {
    }

    /**
     * @OAS\Post(path="/user/createWithArray",
     *   tags={"user"},
     *   summary="Creates list of users with given input array",
     *   description="",
     *   operationId="createUsersWithArrayInput",
     *   @OAS\Parameter(
     *     in="body",
     *     name="body",
     *     description="List of user object",
     *     required=true,
     *     @OAS\Schema(
     *       type="array",
     *       @OAS\Items(ref="#/definitions/User")
     *     )
     *   ),
     *   @OAS\Response(response="default", description="successful operation")
     * )
     */
    public function createUsersWithArrayInput()
    {
    }

    /**
     * @OAS\Post(path="/user/createWithList",
     *   tags={"user"},
     *   summary="Creates list of users with given input array",
     *   description="",
     *   operationId="createUsersWithListInput",
     *   @OAS\Parameter(
     *     in="body",
     *     name="body",
     *     description="List of user object",
     *     required=true,
     *     @OAS\Schema(
     *       type="array",
     *       @OAS\Items(ref="#/definitions/User")
     *     )
     *   ),
     *   @OAS\Response(response="default", description="successful operation")
     * )
     */

    /**
     * @OAS\Get(path="/user/login",
     *   tags={"user"},
     *   summary="Logs user into the system",
     *   description="",
     *   operationId="loginUser",
     *   @OAS\Parameter(
     *     name="username",
     *     in="query",
     *     description="The user name for login",
     *     required=true,
     *     type="string"
     *   ),
     *   @OAS\Parameter(
     *     name="password",
     *     in="query",
     *     description="The password for login in clear text",
     *     required=true,
     *     type="string"
     *   ),
     *   @OAS\Response(
     *     response=200,
     *     description="successful operation",
     *     @OAS\Schema(type="string"),
     *     @OAS\Header(
     *       header="X-Rate-Limit",
     *       type="integer",
     *       format="int32",
     *       description="calls per hour allowed by the user"
     *     ),
     *     @OAS\Header(
     *       header="X-Expires-After",
     *       type="string",
     *       format="date-time",
     *       description="date in UTC when token expires"
     *     )
     *   ),
     *   @OAS\Response(response=400, description="Invalid username/password supplied")
     * )
     */
    public function loginUser()
    {
    }

    /**
     * @OAS\Get(path="/user/logout",
     *   tags={"user"},
     *   summary="Logs out current logged in user session",
     *   description="",
     *   operationId="logoutUser",
     *   parameters={},
     *   @OAS\Response(response="default", description="successful operation")
     * )
     */
    public function logoutUser()
    {
    }

    /**
     * @OAS\Get(path="/user/{username}",
     *   tags={"user"},
     *   summary="Get user by user name",
     *   description="",
     *   operationId="getUserByName",
     *   @OAS\Parameter(
     *     name="username",
     *     in="path",
     *     description="The name that needs to be fetched. Use user1 for testing. ",
     *     required=true,
     *     type="string"
     *   ),
     *   @OAS\Response(response=200, description="successful operation", @OAS\Schema(ref="#/definitions/User")),
     *   @OAS\Response(response=400, description="Invalid username supplied"),
     *   @OAS\Response(response=404, description="User not found")
     * )
     */
    public function getUserByName($username)
    {
    }

    /**
     * @OAS\Put(path="/user/{username}",
     *   tags={"user"},
     *   summary="Updated user",
     *   description="This can only be done by the logged in user.",
     *   operationId="updateUser",
     *   @OAS\Parameter(
     *     name="username",
     *     in="path",
     *     description="name that need to be updated",
     *     required=true,
     *     type="string"
     *   ),
     *   @OAS\Parameter(
     *     in="body",
     *     name="body",
     *     description="Updated user object",
     *     required=true,
     *     @OAS\Schema(ref="#/definitions/User")
     *   ),
     *   @OAS\Response(response=400, description="Invalid user supplied"),
     *   @OAS\Response(response=404, description="User not found")
     * )
     */
    public function updateUser()
    {
    }

    /**
     * @OAS\Delete(path="/user/{username}",
     *   tags={"user"},
     *   summary="Delete user",
     *   description="This can only be done by the logged in user.",
     *   operationId="deleteUser",
     *   @OAS\Parameter(
     *     name="username",
     *     in="path",
     *     description="The name that needs to be deleted",
     *     required=true,
     *     type="string"
     *   ),
     *   @OAS\Response(response=400, description="Invalid username supplied"),
     *   @OAS\Response(response=404, description="User not found")
     * )
     */
    public function deleteUser()
    {
    }
}
