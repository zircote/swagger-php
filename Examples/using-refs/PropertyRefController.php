<?php

namespace OpenApi\Examples\UsingRefs;

class PropertyRefController
{
    /**
     * @OA\Get(
     *     tags={"Products"},
     *     path="/products/{product_id}/do-other-stuff",
     *     @OA\Parameter(ref="#/components/schemas/Product/properties/id"),
     *     @OA\Response(
     *         response="default",
     *         ref="#/components/responses/product"
     *     )
     * )
     */
    public function doStuff($id)
    {
    }
}
