<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'YoYo')]
class ClassRefSpec
{
}

#[OA\Info(title: 'ClassRef', version: '1.0')]
#[OA\Operation\Get(
    path: '/endpoint',
    operationId: 'ClassRefEndpoint',
    responses: [
        new OA\Response(
            response: 200,
            description: 'All good',
            content: new OA\MediaType\Json(ref: ClassRefSpec::class)
        ),
    ]
)]
class ClassRefEndpointSpec
{
}
