<?php

namespace Openapi\Snippets\Cookbook\NamedObject;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Profile',
    properties: [
        new OA\Property(property: 'status', type: 'string'),
        new OA\Property(property: 'group', type: 'object', properties: [
            new OA\Property(property: 'id', type: 'integer'),
            new OA\Property(property: 'name', type: 'string'),
        ]),
    ],
)]
class Profile
{
}
