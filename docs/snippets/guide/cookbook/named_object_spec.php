<?php

namespace Openapi\Snippets\Cookbook\NamedObject;

use OpenApi\Spec as OA;

#[OA\Schema(
    component: 'Profile',
    properties: [
        'status' => OA\Type::String,
        'group' => new OA\Schema(properties: [
            'id' => OA\Type::Integer,
            'name' => OA\Type::String,
        ]),
    ],
)]
class Profile
{
}
