<?php declare(strict_types=1);

namespace OpenApi\Tests\Fixtures\Assembler;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'SimpleProduct')]
class SimpleProduct
{
    #[OA\Property(property: 'name')]
    #[OA\Schema(description: 'The name.')]
    public string $name;

    #[OA\Property(property: 'price')]
    #[OA\Schema(type: 'number', format: 'float')]
    public float $price;
}
