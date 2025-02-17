<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Misc\Annotations;

use OpenApi\Annotations as OA;

/**
 * @OA\Response(
 *     response=200,
 *     description="Success",
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(
 *             @OA\Property(property="name", type="integer", description="demo")
 *         ),
 *         @OA\Examples(example=200, summary="", value={"name": 1}),
 *         @OA\Examples(example=300, summary="", value={"name": 1}),
 *         @OA\Examples(example=400, summary="", value={"name": 1})
 *     )
 * )
 */
class Response
{
}
