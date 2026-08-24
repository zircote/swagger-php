<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Resolver;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'Product')]
class Product
{
    #[OA\Property(property: 'name')]
    public string $name;

    // transitive; picked up via the property type
    #[OA\Property(property: 'manufacturer')]
    public Manufacturer $manufacturer;

    // no spec attributes; not resolvable
    public Weight $weight;
}
