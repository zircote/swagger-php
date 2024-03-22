<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\PathParameter(name: 'item_name', description: 'The item name', required: true, schema: new OAT\Schema(type: 'string'))]
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
        new OAT\Parameter(ref: '#/components/parameters/item_name'),
    ],
    responses: [
        new OAT\Response(response: 200, ref: '#/components/responses/item'),
    ]
)]
class UsingRefsController
{
}
