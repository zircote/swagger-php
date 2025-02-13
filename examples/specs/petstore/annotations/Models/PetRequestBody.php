<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Annotations\Models;

/**
 * @OA\RequestBody(
 *     request="Pet",
 *     description="Pet object that needs to be added to the store",
 *     required=true,
 *     @OA\JsonContent(ref="#/components/schemas/Pet"),
 *     @OA\MediaType(
 *         mediaType="application/xml",
 *         @OA\Schema(ref="#/components/schemas/Pet")
 *     )
 * )
 */
class PetRequestBody
{
}
