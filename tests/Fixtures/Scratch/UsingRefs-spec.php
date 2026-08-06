<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Components]
#[OA\Parameter\Path(name: 'item_name', description: 'The item name', required: true, schema: new OA\Schema(type: 'string'))]
class UsingRefsParameterSpec
{
}

#[OA\Components(
    responses: [
        new OA\Response(response: 'default', description: 'Item response'),
    ]
)]
class UsingRefsResponseSpec
{
}

#[OA\Info(title: 'Parameter Ref', version: '1.0.0')]
#[OA\Operation\Get(
    path: '/item/{item_name}',
    operationId: 'getItem',
    parameters: [
        new OA\Parameter(ref: '#/components/parameters/item_name'),
    ],
    responses: [
        new OA\Response(response: 200, ref: '#/components/responses/default'),
    ]
)]
class UsingRefsControllerSpec
{
}
