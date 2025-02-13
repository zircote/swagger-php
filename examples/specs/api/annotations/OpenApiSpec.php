<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Annotations;

use OpenApi\Annotations as OA;

/**
 * The Spec.
 *
 * @OA\OpenApi(
 *     openapi="3.1.0",
 *     security={{"bearerAuth": {}}}
 * )
 * @OA\Info(
 *     version="1.0.0",
 *     title="Basic single file API",
 *     @OA\License(name="MIT", identifier="MIT", @OA\Attachable)
 * )
 * @OA\Server(
 *     url="https://localhost/api",
 *     description="API server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     description="Basic Auth"
 * )
 * @OA\Tag(
 *     name="products",
 *     description="All about products"
 * )
 * @OA\Tag(
 *     name="catalog",
 *     description="Catalog API"
 * )
 */
class OpenApiSpec
{
}
