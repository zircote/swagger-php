<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Info(
    title: 'Nested Additional Properties',
    version: '1.0'
)]
#[OA\Operation\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'nestedAdditionalProperties',
)]
#[OA\Response(response: 200, description: 'OK')]
#[OA\Schema(
    schema: 'NestedAdditionalProperties',
    additionalProperties: new OA\Schema\AdditionalProperties(
        additionalProperties: new OA\Schema\AdditionalProperties(
            type: 'string',
            additionalProperties: false,
        )
    ),
    type: 'object'
)]
class NestedAdditionalPropertiesSpec
{
}
