<?php

namespace OpenApi\Examples\Specs\UsingRefs\Annotations;

use OpenApi\Annotations as OA;

class PropertyRefController
{
    /**
     * @OA\Get(
     *     tags={"Products"},
     *     path="/products/{product_id}/do-other-stuff",
     *     @OA\Parameter(ref="#/components/schemas/Product/properties/id"),
     *     @OA\Response(
     *         response="default",
     *         ref="#/components/responses/todo"
     *     )
     * )
     */
    public function doStuff($id)
    {
    }
}
