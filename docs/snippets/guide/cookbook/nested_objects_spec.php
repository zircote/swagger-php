<?php

namespace Openapi\Snippets\Cookbook\NestedObjects;

use OpenApi\Spec as OA;

#[OA\Schema(
    schema: 'Profile',
    type: 'object',
    properties: [
        new OA\Property(
            property: 'Status',
            schema: new OA\Schema(
                type: 'string',
                example: '0',
            ),
        ),
        new OA\Property(
            property: 'Group',
            schema: new OA\Schema(
                type: 'object',
                properties: [
                new OA\Property(
                    property: 'ID',
                    schema: new OA\Schema(
                        description: 'ID de grupo',
                        type: 'number',
                        example: -1,
                    ),
                ),
                new OA\Property(
                    property: 'Name',
                    schema: new OA\Schema(
                        description: 'Nombre de grupo',
                        type: 'string',
                        example: 'Superadmin',
                    ),
                ),
            ],
            ),
        ),
    ],
)]
class OpenApiSpec
{
}
