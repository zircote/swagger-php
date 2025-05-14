<?php

use OpenApi\Annotations as OA;

class Controller
{
    /**
     * @OA\Get(
     *     path="/api/endpoint",
     *     description="The endpoint",
     *     operationId="endpoint",
     *     tags={"endpoints"},
     *     @OA\Parameter(
     *         name="things[]",
     *         in="query",
     *         description="A list of things.",
     *         required=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="integer")
     *         )
     *     ),
     *     @OA\Response(response="200", description="All good")
     * )
     */
    public function endpoint()
    {
        // ...
    }
}
