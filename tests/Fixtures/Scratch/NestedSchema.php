<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Info(
    title: 'Parameter Content Scratch',
    version: '1.0'
)]
#[OAT\Get(
    path: '/api/endpoint',
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
