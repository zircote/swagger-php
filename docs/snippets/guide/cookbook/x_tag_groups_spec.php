<?php

namespace Openapi\Snippets\Cookbook\XTagGroups;

use OpenApi\Spec as OA;

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
