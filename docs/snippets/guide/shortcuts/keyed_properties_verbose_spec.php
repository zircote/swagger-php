<?php

namespace Openapi\Snippets\Shortcuts\KeyedProperties;

use OpenApi\Spec as OA;

class ProductController
{
    #[OA\Operation\Get(path: '/products/{id}')]
    #[OA\Response(response: 200, description: 'The product', content: [
        new OA\MediaType\Json(
            type: 'object',
            properties: [
                new OA\Property(property: 'id', schema: new OA\Schema(type: 'integer')),
                new OA\Property(property: 'name', schema: new OA\Schema(type: 'string')),
                new OA\Property(property: 'createdAt', schema: new OA\Schema(type: 'string', format: 'date-time')),
            ],
        ),
    ])]
    public function show(int $id): array
    {
        // ...
    }
}
