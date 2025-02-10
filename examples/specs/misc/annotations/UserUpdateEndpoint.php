<?php

namespace OpenApi\Examples\Specs\Misc\Annotations;

use OpenApi\Annotations as OA;

/**
 * @OA\Put(
 *     path="/users/{id}",
 *     summary="Updates a user",
 *     description="Updates a user",
 *     operationId="updateUser",
 *     tags={"user"},
 *     @OA\Parameter(
 *         description="Parameter with mutliple examples",
 *         in="path",
 *         name="id",
 *         required=true,
 *         @OA\Schema(type="string"),
 *         @OA\Examples(example="int", value="1", summary="An int value."),
 *         @OA\Examples(example="uuid", value="0006faf6-7a61-426c-9034-579f2cfcfa83", summary="An UUID value."),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OK"
 *     )
 * )
 */
class UserUpdateEndpoint
{
}
