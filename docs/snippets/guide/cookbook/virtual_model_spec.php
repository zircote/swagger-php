<?php

namespace Openapi\Snippets\Cookbook\VirtualModel;

use OpenApi\Spec as OA;

#[OA\Schema(
    properties: [
        'name' => new OA\Property(property: 'name', schema: new OA\Schema(type: 'string')),
        'email' => new OA\Property(property: 'email', schema: new OA\Schema(type: 'string')),
    ]
)]
class User
{
}
