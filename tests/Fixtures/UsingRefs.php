<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Using a parameter definition", version="unittest")
 */
class UsingRefs
{
    /**
     * @OA\Get(
     *     path="/pi/{item_name}",
     *     summary="Get protected item",
     *     @OA\Parameter(ref="#/components/parameters/ItemName"),
     *     @OA\Response(
     *         response="default",
     *         ref="#/components/responses/Item"
     *     )
     * )
     */
    public function getProtectedItem()
    {
    }
}

/**
 * @OA\Parameter(
 *     name="ItemName",
 *     in="path",
 *     required=true,
 *     description="protected item name",
 * )
 */
class UsingRefsParameter
{
}

/**
 * @OA\Response(
 *     response="Item",
 *     description="A protected item"
 * )
 */
class UsingRefsResponse
{
}
