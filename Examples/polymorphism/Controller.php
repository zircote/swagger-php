<?php

namespace OpenApi\Examples\Polymorphism;

use OpenApi\Annotations as OA;

class Controller
{
    /**
     * @OA\Info(title="Polymorphism", version="1")
     *
     * @OA\Get(
     *     path="/test",
     *     @OA\Response(
     *         response="default",
     *         description="Polymorphism",
     *         @OA\JsonContent(ref="#/components/schemas/Request")
     *     )
     * )
     */
    public function getProduct($id)
    {
    }
}
