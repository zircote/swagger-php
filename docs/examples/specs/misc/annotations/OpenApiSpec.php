<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Misc\Annotations;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     security={{"bearerAuth": {}}},
 *     @OA\Tag(
 *         name="endpoints"
 *     )
 * )
 * @OA\Info(
 *     title="Testing annotations from bugreports",
 *     version="1.0.0",
 *     description="NOTE:
This sentence is on a new line",
 *     @OA\Contact(name="Swagger API Team"),
 *     @OA\License(
 *         name="apache",
 *         url="https://github.com/zircote/swagger-php/blob/master/LICENSE"
 *     )
 * )
 * @OA\Components(
 *     @OA\SecurityScheme(
 *         securityScheme="bearerAuth",
 *         type="http",
 *         scheme="bearer",
 *     ),
 *     @OA\Attachable
 * )
 * @OA\Server(
 *     url="{schema}://host.dev",
 *     description="OpenApi parameters",
 *     @OA\ServerVariable(
 *         serverVariable="schema",
 *         enum={"https", "http"},
 *         default="https"
 *     )
 * )
 */
class OpenApiSpec
{
}
