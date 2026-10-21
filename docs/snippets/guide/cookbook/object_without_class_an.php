<?php

namespace Openapi\Snippets\Cookbook\ObjectWithoutClass;

use OpenApi\Annotations as OA;

class UserController
{
    /**
     * @OA\Get(
     *     path="/users/me",
     *     @OA\Response(
     *         response=200,
     *         description="The signed in user",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="lastLogin", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function me()
    {
    }
}
