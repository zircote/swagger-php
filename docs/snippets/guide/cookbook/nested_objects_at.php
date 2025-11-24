<?php

namespace Openapi\Snippets\Cookbook\NestedObjects;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Profile',
    type: 'object',
    properties: [
        new OA\Property(
            property: 'Status',
            type: 'string',
            example: '0',
        ),
        new OA\Property(
            property: 'Group',
            type: 'object',
            properties: [
                new OA\Property(
                    property: 'ID',
                    description: 'ID de grupo',
                    type: 'number',
                    example: -1,
                ),
                new OA\Property(
                    property: 'Name',
                    description: 'Nombre de grupo',
                    type: 'string',
                    example: 'Superadmin'
                ),
            ],
        ),
    ],
)]
class OpenApiSpec
{
}
