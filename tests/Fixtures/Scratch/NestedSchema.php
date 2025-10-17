<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class NestedSchemaOne
{
}

#[OAT\Schema]
class NestedSchemaTwo
{
}

#[OAT\Info(
    title: 'Parameter Content Scratch',
    version: '1.0'
)]
#[OAT\Post(
    path: '/api/endpoint',
    requestBody: new OAT\RequestBody(content: [new OAT\MediaType(
        mediaType: 'application/json',
        schema: new OAT\Schema(
            required: ['note'],
            properties: [
                new OAT\Property(property: 'note', example: 'My note'),
                new OAT\Property(
                    property: 'other',
                    description: 'other',
                    oneOf: [
                        new OAT\Schema(type: NestedSchemaOne::class),
                        new OAT\Schema(type: NestedSchemaTwo::class),
                    ]
                ),
            ]
        )
    )]),
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
#[OAT\Schema(
    required: ['errors'],
    properties: [
        new OAT\Property(
            property: 'errors',
            description: 'Validation errors',
            type: 'object',
            minItems: 1,
            uniqueItems: true,
            additionalProperties: new OAT\AdditionalProperties(
                description: 'Array of error messages for property',
                type: 'array',
                items: new OAT\Items(
                    type: 'string',
                ),
                minItems: 1,
                uniqueItems: true,
            ),
        ),
    ],
    type: 'object'
)]
class NestedSchema
{
}
