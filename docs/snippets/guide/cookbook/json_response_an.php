<?php

namespace Openapi\Snippets\Cookbook\JsonResponse;

use OpenApi\Annotations as OA;

class PetController
{
    /**
     * @OA\Get(
     *     path="/pets/current",
     *     @OA\Response(
     *         response=200,
     *         description="The current pet",
     *         @OA\JsonContent(ref="#/components/schemas/Pet")
     *     )
     * )
     */
    public function current()
    {
    }
}
