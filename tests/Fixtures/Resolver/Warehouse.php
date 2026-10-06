<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Resolver;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'Warehouse')]
class Warehouse
{
    // reached only by resolving the property type, and fails to collect
    #[OA\Property(property: 'stock')]
    public UnloadableStock $stock;

    // resolved all the same
    #[OA\Property(property: 'manufacturer')]
    public Manufacturer $manufacturer;
}
