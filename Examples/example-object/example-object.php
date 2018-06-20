<?php

use Swagger\Annotations as OAS;

/**
 * @OAS\Info(
 *     version="1.0",
 *     title="Example for response examples value"
 * )
 */

/**
 * @OAS\Post(
 *     path="/users",
 *     summary="Adds a new user",
 *     @OAS\RequestBody(
 *         @OAS\MediaType(
 *             mediaType="application/json",
 *             @OAS\Schema(
 *                 @OAS\Property(
 *                     property="id",
 *                     type="string"
 *                 ),
 *                 @OAS\Property(
 *                     property="name",
 *                     type="string"
 *                 ),
 *                 example={"id": 10, "name": "Jessica Smith"}
 *             )
 *         )
 *     ),
 *     @OAS\Response(
 *         response=200,
 *         description="OK"
 *     )
 * )
 */
