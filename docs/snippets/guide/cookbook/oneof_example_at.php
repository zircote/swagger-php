<?php

use OpenApi\Attributes as OA;

class Controller
{
    #[OA\Response(
        response: 200,
        content: new OA\JsonContent(
            oneOf: [
                new OA\Schema(ref: "#/components/schemas/QualificationHolder"),
                new OA\Schema(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/QualificationHolder")
                ),
            ],
        ),
    )]
    public function index()
    {
        // ...
    }
}
