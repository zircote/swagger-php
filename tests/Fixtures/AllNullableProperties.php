<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class AllNullableProperties
{
    #[OAT\Property]
    public ?string $a;

    #[OAT\Property]
    public ?int $b;
}
