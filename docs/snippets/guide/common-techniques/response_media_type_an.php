<?php

use OpenApi\Annotations as OA;

class Controller
{
    /**
     * @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(ref="#/components/schemas/User"),
     *     )
     * ),
     */
    public function endpoint() {}
}
