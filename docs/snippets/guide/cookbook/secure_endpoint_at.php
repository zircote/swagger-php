<?php

use OpenApi\Attributes as OA;

class Controller
{
    #[OA\Get(
        path: '/api/secure/',
        summary: 'Requires authentication',
        security: [
            [
                'api_key' => [],
            ],
        ],
    )]
    public function getSecurely()
    {
        // ...
    }
}
