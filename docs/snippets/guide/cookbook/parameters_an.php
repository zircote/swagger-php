<?php

namespace Openapi\Snippets\Cookbook\Parameters;

use OpenApi\Annotations as OA;

class PetController
{
    /**
     * @OA\Get(
     *     path="/pets/{id}",
     *     @OA\PathParameter(name="id", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="fields", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="X-Request-Id", in="header", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="The pet")
     * )
     */
    public function show()
    {
    }
}
