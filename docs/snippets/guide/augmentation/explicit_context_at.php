<?php

namespace Openapi\Snippets\Augmentation\Explicit;

use OpenApi\Attributes as OA;

#[OA\Schema]
class Product
{
    /**
     * The product name.
     */
    #[OA\Property(
        property: 'name',
        type: 'string',
        description: 'The product name'
    )]
    public string $name;
}
