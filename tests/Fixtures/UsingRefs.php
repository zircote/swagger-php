<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'Using a parameter definition', version: 'unittest')]
class UsingRefs
{
    #[OAT\Get(
        path: '/pi/{item_name}',
        summary: 'Get protected item',
        parameters: [new OAT\Parameter(ref: '#/components/parameters/ItemName')],
        responses: [new OAT\Response(response: 'default', ref: '#/components/responses/default')],
    )]
    public function getProtectedItem()
    {
    }
}

#[OAT\Parameter(name: 'ItemName', in: 'path', required: true, description: 'protected item name')]
class UsingRefsParameter
{
}

#[OAT\Response(response: 'default', description: 'A protected item')]
class UsingRefsResponse
{
}
