<?php

namespace Openapi\Snippets\Cookbook\OperationsPerMethod;

use OpenApi\Annotations as OA;

class PetController
{
    /**
     * @OA\Get(
     *     path="/pets",
     *     @OA\Response(response=200, description="All pets")
     * )
     */
    public function list()
    {
    }

    /**
     * @OA\Post(
     *     path="/pets",
     *     @OA\Response(response=201, description="The new pet")
     * )
     */
    public function create()
    {
    }
}
