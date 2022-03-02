<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

/**
 * @OA\Info(title="Duplicate operationId", version="unittest")
 */
class DuplicateOperationId
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

    /**
     * @OA\Get(
     *     path="/admin/items/{item_name}",
     *     summary="Get item",
     *     operationId="getItem",
     *     @OA\PathParameter(name="item_name"),
     *     @OA\Response(response="default", description="OK")
     * )
     */
    public function getAdminItem()
    {
    }
}
