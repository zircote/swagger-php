<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Resolver;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'Shelf')]
class Shelf
{
    // the schema implies the property, so the type is chased
    #[OA\Schema(description: 'Who stocks it')]
    public Manufacturer $vendor;
}
