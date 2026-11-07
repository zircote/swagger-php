<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Resolver;

use OpenApi\Spec as OA;

trait Stocked
{
    #[OA\Property(property: 'maker')]
    public Manufacturer $maker;
}
