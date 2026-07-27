<?php

namespace Openapi\Snippets\Augmentation\Explicit;

use OpenApi\Spec as OA;

#[OA\Schema]
class Product
{
    /**
     * The product name.
     */
    #[OA\Property(property: 'name')]
    #[OA\Schema(
        type: 'string',
        description: 'The product name'
    )]
    public string $name;
}
