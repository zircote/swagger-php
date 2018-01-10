<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace Petstore30\controllers;


/**
 * Class User
 *
 * @package Petstore30\controllers
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class User
{
    /**
     * @OAS\Post(
     *     path="/user",
     *     tags={"user"},
     *     summary="Create user",
     *     description="This can only be done by the logged in user.",
     *     operationId="createUser",
     *     @OAS\Response(
     *         response="default",
     *         description="successful operation"
     *     ),
     *     @OAS\RequestBody(
     *         description="Create user object",
     *         required=true,
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(
     *                 ref="#/components/schemas/User"
     *             )
     *         )
     *     )
     * )
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function createUser()
    {
    }

    /**
     * @OAS\Post(
     *     path="/user/createWithArray",
     *     tags={"user"},
     *     summary="Create list of users with given input array",
     *     operationId="createUsersWithListInput",
     *     @OAS\Response(
     *         response="default",
     *         description="successful operation"
     *     ),
     *     @OAS\RequestBody(
     *         ref="#/components/requestBodies/UserArray"
     *     )
     * )
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function createUsersWithListInput()
    {
    }

    /**
     * @OAS\Get(
     *     path="/user/login",
     *     tags={"user"},
     *     summary="Logs user into system",
     *     operationId="loginUser",
     *     @OAS\Parameter(
     *         name="username",
     *         in="query",
     *         description="The user name for login",
     *         required=true,
     *         @OAS\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OAS\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         @OAS\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OAS\Response(
     *         response=200,
     *         description="successful operation",
     *         @OAS\Header(
     *             header="X-Rate-Limit",
     *             description="calls per hour allowed by the user",
     *             type="integer",
     *             format="int32"
     *         ),
     *         @OAS\Header(
     *             header="X-Expires-After",
     *             description="date in UTC when token expires",
     *             type="string",
     *             format="datetime"
     *         ),
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(
     *                 type="string"
     *             )
     *         ),
     *         @OAS\MediaType(
     *             mediaType="application/xml",
     *             @OAS\Schema(
     *                 type="string"
     *             )
     *         )
     *     ),
     *     @OAS\Response(
     *         response=400,
     *         description="Invalid username/password supplied"
     *     )
     * )
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function loginUser()
    {
    }

    /**
     * @OAS\Get(
     *     path="/user/logout",
     *     tags={"user"},
     *     summary="Logs out current logged in user session",
     *     operationId="logoutUser",
     *     @OAS\Response(
     *         response="default",
     *         description="successful operation"
     *     )
     * )
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function logoutUser()
    {
    }

    /**
     * @OAS\Get(
     *     path="/user/{username}",
     *     summary="Get user by user name",
     *     operationId="getUserByName",
     *     @OAS\Parameter(
     *         name="username",
     *         in="path",
     *         required=true,
     *         @OAS\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OAS\Response(
     *         response=200,
     *         description="successful operation",
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(
     *                 ref="#/components/schemas/User"
     *             )
     *         ),
     *         @OAS\MediaType(
     *             mediaType="application/xml",
     *             @OAS\Schema(
     *                 ref="#/components/schemas/User"
     *             )
     *         )
     *     ),
     *     @OAS\Response(
     *         response=400,
     *         description="Invalid username supplied"
     *     ),
     *     @OAS\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     * )
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function getUserByName()
    {
    }

    /**
     * @OAS\Put(
     *     path="/user/{username}",
     *     summary="Updated user",
     *     description="This can pnly be done by the logged in user.",
     *     operationId="updateUser",
     *     @OAS\Parameter(
     *         name="username",
     *         in="path",
     *         description="name that to be updated",
     *         required=true,
     *         @OAS\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OAS\Response(
     *         response=400,
     *         description="Invalid user supplied"
     *     ),
     *     @OAS\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OAS\RequestBody(
     *         description="Updated user object",
     *         required=true,
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(
     *                 ref="#/components/schemas/User"
     *             )
     *         )
     *     )
     * )
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function updateUser()
    {
    }

    /**
     * @OAS\Delete(
     *     path="/user/{username}",
     *     summary="Delete user",
     *     description="This can only be done by the logged in user.",
     *     operationId="deleteUser",
     *     @OAS\Parameter(
     *         name="username",
     *         in="path",
     *         description="The name that needs to be deleted",
     *         required=true,
     *         @OAS\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OAS\Response(
     *         response=400,
     *         description="Invalid username supplied",
     *     ),
     *     @OAS\Response(
     *         response=404,
     *         description="User not found",
     *     )
     * )
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function deleteUser()
    {
    }
}