<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Misc\Annotations;

use OpenApi\Annotations as OA;

/**
 * An API endpoint.
 *
 * @OA\Get(
 *     path="/api/endpoint",
 *     description="An endpoint",
 *     operationId="endpoint",
 *     tags={"endpoints"},
 *     @OA\Parameter(name="filter", in="query", @OA\JsonContent(
 *         @OA\Property(property="type", type="string"),
 *         @OA\Property(property="color", type="string"),
 *     )),
 *     security={{ "bearerAuth": {} }},
 *     @OA\Response(response="200", ref="#/components/responses/200")
 * )
 */
class Endpoint
{
}
