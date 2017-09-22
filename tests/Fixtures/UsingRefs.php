<?php declare(strict_types=1);
namespace SwaggerFixtures;

/**
 * @OAS\Info(title="Using a parameter definition", version="unittest")
 */
class UsingRefs
{

    /**
     * @OAS\Get(
     *   path="/pi/{item_name}",
     *   summary="Get protected item",
     *   @OAS\Parameter(ref="#/parameters/ItemName"),
     *   @OAS\Response(response="default", ref="#/responses/Item")
     * )
     */
    public function getProtectedItem()
    {
    }
}

/**
 * @OAS\Parameter(
 *   name="ItemName",
 *   in="path",
 *   required=true,
 *   description="protected item name",
 * )
 */

/**
 * @OAS\Response (
 *   response="Item",
 *   description="A protected item"
 * )
 */
