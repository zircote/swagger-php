<?php

namespace Openapi\Snippets\Cookbook\JsonResponse;

use OpenApi\Attributes as OA;

class PetController
{
    #[OA\Get(path: '/pets/current')]
    #[OA\Response(
        response: 200,
        description: 'The current pet',
        content: new OA\JsonContent(ref: '#/components/schemas/Pet'),
    )]
    public function current()
    {
    }
}
