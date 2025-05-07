<?php

use OpenApi\Attributes as OA;

#[OA\Schema()]
class Product
{
    /**
     * The product name
     * @var string
     */
    #[OA\Property(
        property: 'name',
        type: 'string',
        description: 'The product name'
    )]
    public string $name;
}
