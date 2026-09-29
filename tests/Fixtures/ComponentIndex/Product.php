<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ComponentIndex;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'product')]
class Product
{
    #[OA\Property]
    public string $name;
}
