<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\PathParameter(name: 'itemName', description: 'The item name')]
class UsingRefsParameter
{
}

#[OAT\Response(response: 'item', description: 'Item response')]
class UsingRefsResponse
{
}

#[OAT\Info(title: 'Parameter Ref', version: '1.0.0')]
#[OAT\Get(
    path: '/item/{item_name}',
    parameters: [
        new OAT\Parameter(ref: '#/components/parameters/itemName'),
    ],
    responses: [
        new OAT\Response(response: 200, ref: '#/components/responses/item'),
    ]
)]
class UsingRefsController
{
}
