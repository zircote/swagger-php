<?php

namespace Openapi\Snippets\Cookbook\VirtualModel;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *     ),
 * )
 */
class User
{
}
