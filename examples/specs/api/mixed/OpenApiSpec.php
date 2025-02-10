<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Mixed;

use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;

/**
 * The Spec.
 *
 * @OA\OpenApi(
 *     openapi="3.1.0",
 *     @OA\Info(
 *         version="1.0.0",
 *         title="Basic single file API",
 *         @OA\License(name="MIT", identifier="MIT")
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     description="Basic Auth"
 * )
 */
#[OAT\Server(url: 'https://localhost/api', description: 'API server')]
#[OAT\Tag(name: 'products', description: 'All about products')]
#[OAT\Tag(name: 'catalog', description: 'Catalog API')]
class OpenApiSpec
{
}
