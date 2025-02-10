<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Misc\Annotations;

use OpenApi\Annotations as OA;

/**
 * Another API endpoint.
 *
 * @OA\Get(
 *     path="/api/anotherendpoint",
 *     description="Another endpoint",
 *     operationId="anotherendpoints",
 *     tags={"endpoints"},
 *     @OA\Parameter(
 *         name="things[]",
 *         in="query",
 *         description="A list of things.",
 *         required=false,
 *         @OA\Schema(
 *             type="array",
 *             @OA\Items(type="integer")
 *         )
 *     ),
 *     security={{ "bearerAuth": {} }},
 *     @OA\Response(response="200", ref="#/components/responses/200")
 * )
 */
class MultiValueQueryParamEndpoint
{
}
