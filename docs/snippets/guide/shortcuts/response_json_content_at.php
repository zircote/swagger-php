<?php

namespace Openapi\Snippets\Shortcuts\ResponseJson;

use OpenApi\Attributes as OA;

class Controller
{
    #[OA\Response(
        response: 200,
        description: 'successful operation',
        content: new OA\JsonContent(
            ref: '#/components/schemas/User',
        ),
    )]
    public function endpoint()
    {
    }
}
