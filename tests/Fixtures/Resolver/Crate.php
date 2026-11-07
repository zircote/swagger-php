<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Resolver;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'Crate')]
class Crate
{
    public function __construct(
        // a promoted parameter, chased by its parameter's type
        #[OA\Property(property: 'maker')]
        public Manufacturer $maker,
        // an enum list, which the PHP type does not reference
        #[OA\Property(property: 'supplier', schema: new OA\Schema(enum: ['acme']))]
        public Weight $supplier,
    ) {
    }
}
