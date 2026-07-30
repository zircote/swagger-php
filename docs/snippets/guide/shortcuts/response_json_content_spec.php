<?php

namespace Openapi\Snippets\Shortcuts\ResponseJson;

use OpenApi\Spec as OA;

class ControllerSpec
{
    #[OA\Operation\Get(path: '/endpoint')]
    #[OA\Response(
        response: 200,
        description: 'successful operation',
        content: [new OA\MediaType\Json(
            ref: '#/components/schemas/User',
        )],
    )]
    public function endpoint()
    {
    }
}
