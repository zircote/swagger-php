<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Resolver;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'Manufacturer')]
class Manufacturer
{
    #[OA\Property(property: 'name')]
    public string $name;
}
