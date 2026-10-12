<?php

namespace Openapi\Snippets\Shortcuts\KeyedProperties;

use OpenApi\Spec as OA;

class ProductController
{
    #[OA\Operation\Get(path: '/products/{id}')]
    #[OA\Response(response: 200, description: 'The product', content: [
        new OA\MediaType\Json(
            type: OA\Type::Object,
            properties: [
                'id' => OA\Type::Integer,
                'name' => OA\Type::String,
                'createdAt' => OA\Format::DateTime,
            ],
        ),
    ])]
    public function show(int $id): array
    {
        // ...
    }
}
