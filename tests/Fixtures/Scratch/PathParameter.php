<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;

/**
 * @OA\Info(title="Path parameter", version="1.0")
 */
class PathParameter
{
    /**
     * @OA\Get(
     *     path="/items/{item_name}",
     *     summary="Get item",
     *     operationId="getItem",
     *     @OA\PathParameter(name="item_name"),
     *     @OA\Response(response="default", description="OK")
     * )
     */
    public function getItem()
    {
    }

    #[OAT\Get(
        path: '/admin/items/{item_name}',
        summary: 'Get admin item',
        operationId: 'getAdminItem',
    )]
    #[OAT\PathParameter(name: 'item_name')]
    #[OAT\Response(response: 'default', description: 'OK')]
    public function getAdminItem()
    {
    }
}
