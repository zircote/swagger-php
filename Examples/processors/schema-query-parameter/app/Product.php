<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Product',
    description: 'A simple product model',
)]
class Product
{
    public function __construct(
        #[OA\Property(
            title: 'The unique identifier of a product in our catalog.',
            example: 43,
        )]
        public int $id,
        #[OA\Property(
            title: 'The name of the product.',
            example: 'Lorem ipsum',
        )]
        public string|null $name,
    ) {
    }
}
