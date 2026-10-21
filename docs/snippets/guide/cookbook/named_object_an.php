<?php

namespace Openapi\Snippets\Cookbook\NamedObject;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Profile",
 *     @OA\Property(property="status", type="string"),
 *     @OA\Property(
 *         property="group",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string")
 *     )
 * )
 */
class Profile
{
}
