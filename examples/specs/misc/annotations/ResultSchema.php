<?php

namespace OpenApi\Examples\Specs\Misc\Annotations;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Result",
 *     title="Sample schema for using references",
 * 	@OA\Property(
 *         property="status",
 *         type="string"
 *     ),
 * 	@OA\Property(
 *         property="error",
 *         type="string"
 *     )
 * )
 */
class ResultSchema
{
}
