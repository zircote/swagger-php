<?php

namespace OpenApi\Examples\SwaggerSpec\Petstore;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.0",
 *         title="Swagger Petstore",
 *         @OA\License(name="MIT", identifier="MIT")
 *     ),
 *     @OA\Server(
 *         description="Api server",
 *         url="petstore.swagger.io"
 *     )
 * )
 */
class OpenApiSpec
{
}

/**
 * @OA\Schema(
 *     schema="Error",
 *     required={"code", "message"},
 *     @OA\Property(
 *         property="code",
 *         type="integer",
 *         format="int32"
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string"
 *     )
 * )
 */
class Error
{
}

/**
 * @OA\Schema(
 *     schema="Pets",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/Pet")
 * )
 */
class Pets
{
}
