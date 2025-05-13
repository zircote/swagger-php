<?php

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    x: [
        'tagGroups' => [
            ['name' => 'User Management', 'tags' => ['Users', 'API keys', 'Admin']],
        ],
    ]
)]
class OpenApiSpec
{
}
