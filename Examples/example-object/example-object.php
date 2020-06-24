<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="Example for response examples value",
 *     description="Example for response examples value",
 *     version="1.0.0",
 *     @OA\Contact(
 *         email="examples@swagger.php"
 *     )
 * )
 */

/**
 * @OA\Server(
 *     url="{schema}://host.dev",
 *     description="OpenApi server details",
 *     @OA\ServerVariable(
 *         serverVariable="schema",
 *         enum={"https", "http"},
 *         default="https"
 *     )
 * )
 */
/**
 * @OA\Tag(
 *     name="user",
 *     description="All user related endpoints"
 * )
 */

/**
 * @OA\Post(
 *     path="/users",
 *     summary="Adds a new user",
 *     description="Creates a new user in the database",
 *     operationId="add_user",
 *     tags={"user"},
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="id",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="name",
 *                     type="string"
 *                 ),
 *                 example={"id": "10", "name": "Jessica Smith"}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OK"
 *     )
 * )
 */
