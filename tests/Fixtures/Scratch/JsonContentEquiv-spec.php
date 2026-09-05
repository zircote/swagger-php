<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'JsonContentEquiv')]
class JsonContentEquivSpec
{
}

#[OA\Info(title: 'JsonContentEquiv', version: '1.0')]
#[OA\Operation\Get(
    path: '/endpoint/json-content',
    operationId: 'jsonContentEquiv1',
)]
#[OA\Response(
    response: 200,
    description: 'All good',
    content: [
        new OA\MediaType\Json(ref: JsonContentEquivSpec::class),
    ]
)]
class JsonContentEquivEndpoint1Spec
{
}

#[OA\Operation\Get(
    path: '/endpoint/media-type',
    operationId: 'jsonContentEquiv2',
)]
#[OA\Response(
    response: 200,
    description: 'All good',
    content: [
        new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(ref: JsonContentEquivSpec::class)
        ),
    ]
)]
class JsonContentEquivEndpoint2Spec
{
}
