<?php

namespace Openapi\Snippets\Cookbook\OneOf;

use OpenApi\Spec as OA;

class Controller
{
    #[OA\Response(
        response: 200,
        content: [new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(
                oneOf: [
                new OA\Schema(ref: '#/components/schemas/QualificationHolder'),
                new OA\Schema(
                    type: 'array',
                    items: new OA\Schema(ref: '#/components/schemas/QualificationHolder')
                ),
            ],
            ),
        )],
    )]
    public function index()
    {
        // ...
    }
}
