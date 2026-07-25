<?php

namespace Openapi\Snippets\Cookbook\UnionTypes;

use OpenApi\Spec as OA;

#[OA\Schema(
    schema: 'StringList',
    properties: [
        new OA\Property(
            property: 'value',
            schema: new OA\Schema(
                type: 'array',
                items: new OA\Schema(
                    anyOf: [new OA\Schema(type: 'string')]
                ),
            ),
        ),
    ],
)]
#[OA\Schema(
    schema: 'String',
    properties: [
        new OA\Property(
            property: 'value',
            schema: new OA\Schema(type: 'string'),
        ),
],
)]
#[OA\Schema(
    schema: 'Object',
    properties: [
        new OA\Property(
            property: 'value',
            schema: new OA\Schema(type: 'object'),
        ),
],
)]
#[OA\Schema(
    schema: 'mixedList',
    properties: [
        new OA\Property(
            property: 'fields',
            schema: new OA\Schema(
                type: 'array',
                items: new OA\Schema(
                    oneOf: [
                    new OA\Schema(ref: '#/components/schemas/StringList'),
                    new OA\Schema(ref: '#/components/schemas/String'),
                    new OA\Schema(ref: '#/components/schemas/Object'),
                ],
                ),
            ),
        ),
    ],
)]
class OpenApiSpec
{
}
