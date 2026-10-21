<?php

namespace Openapi\Snippets\Cookbook\JsonResponse;

use OpenApi\Spec as OA;

class PetController
{
    #[OA\Operation\Get(path: '/pets/current')]
    #[OA\Response(
        response: 200,
        description: 'The current pet',
        content: [new OA\MediaType\Json(ref: '#/components/schemas/Pet')],
    )]
    public function current()
    {
    }
}
