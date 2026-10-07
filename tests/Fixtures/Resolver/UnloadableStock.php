<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Resolver;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'UnloadableStock')]
class UnloadableStock
{
    public function __construct(
        // spec's `enum` is a list, so a bare class string fails when the attribute is instantiated
        #[OA\Property(property: 'unit', schema: new OA\Schema(enum: UnloadableStock::class))]
        public string $unit,
    ) {
    }
}
