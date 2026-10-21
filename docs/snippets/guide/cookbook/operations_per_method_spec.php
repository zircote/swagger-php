<?php

namespace Openapi\Snippets\Cookbook\OperationsPerMethod;

use OpenApi\Spec as OA;

class PetController
{
    #[OA\Operation\Get(path: '/pets')]
    #[OA\Response(response: 200, description: 'All pets')]
    public function list()
    {
    }

    #[OA\Operation\Post(path: '/pets')]
    #[OA\Response(response: 201, description: 'The new pet')]
    public function create()
    {
    }
}
