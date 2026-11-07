<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Resolver;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'Pallet')]
class Pallet
{
    // declared first, so resolved first
    public function __construct(
        #[OA\Property(property: 'maker')]
        public Manufacturer $maker,
    ) {
    }

    #[OA\Property(property: 'catalogue')]
    public Catalogue $catalogue;
}
