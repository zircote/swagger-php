<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Resolver;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'Catalogue')]
class Catalogue
{
    #[OA\Property(property: 'name')]
    public string $name;

    // not documented, so not chased, although Manufacturer has attributes
    public Manufacturer $supplier;

    // typed by the attribute, so not chased either
    #[OA\Property(property: 'maker', schema: new OA\Schema(type: 'string'))]
    public Manufacturer $maker;
}
