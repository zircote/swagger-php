<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Info(
    title: 'Nested Additional Properties',
    version: '1.0'
)]
#[OAT\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
#[OAT\Schema(
    additionalProperties: new OAT\AdditionalProperties(
        additionalProperties: new OAT\AdditionalProperties(
            type: 'string',
        )
    ),
    type: 'object'
)]
class NestedAdditionalAttributes
{
}
