<?php

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'StringList',
    properties: [
        new OA\Property(
            property: 'value',
            type: 'array',
            items: new OA\Items(
                anyOf: [new OA\Schema(type: 'string')]
            ),
        ),
    ],
)]
#[OA\Schema(
    schema: 'String',
    properties: [
        new OA\Property(
            property: 'value',
            type: 'string',
        ),
],
)]
#[OA\Schema(
    schema: 'Object',
    properties: [
        new OA\Property(
            property: 'value',
            type: 'object',
        ),
],
)]
#[OA\Schema(
    schema: 'mixedList',
    properties: [
        new OA\Property(
            property: 'fields',
            type: 'array',
            items: new OA\Items(
                oneOf: [
                    new OA\Schema(ref: '#/components/schemas/StringList'),
                    new OA\Schema(ref: '#/components/schemas/String'),
                    new OA\Schema(ref: '#/components/schemas/Object'),
                ],
            ),
        ),
    ],
)]
class OpenApiSpec
{
}
