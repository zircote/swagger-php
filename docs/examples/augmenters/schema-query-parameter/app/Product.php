<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace App;

use OpenApi\Spec as OA;

#[OA\Schema(
    title: 'Product',
    description: 'A simple product model',
)]
class Product
{
    public function __construct(
        #[OA\Property]
        #[OA\Schema(
            title: 'The unique identifier of a product in our catalog.',
            example: 43,
        )]
        public int $id,
        #[OA\Property]
        #[OA\Schema(
            title: 'The name of the product.',
            example: 'Lorem ipsum',
        )]
        public string|null $name,
    ) {
    }
}
