<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Path parameter', version: '1.0')]
class PathParameterSpec
{
    #[OA\Operation\Get(
        path: '/items/{item_name}',
        summary: 'Get item',
        operationId: 'getItem',
    )]
    #[OA\Parameter\Path(name: 'item_name')]
    #[OA\Response(response: 'default', description: 'OK')]
    public function getItem()
    {
    }

    #[OA\Operation\Get(
        path: '/admin/items/{item_name}',
        summary: 'Get admin item',
        operationId: 'getAdminItem',
    )]
    #[OA\Parameter(name: 'item_name', in: OA\ParameterIn::Path, required: true)]
    #[OA\Response(response: 'default', description: 'OK')]
    public function getAdminItem()
    {
    }
}
