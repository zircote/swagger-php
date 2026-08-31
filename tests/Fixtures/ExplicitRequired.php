<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class ExplicitRequired
{
    #[OAT\Property(required: true)]
    public ?string $explicitlyRequired;

    #[OAT\Property(required: false)]
    public string $explicitlyOptional;

    #[OAT\Property]
    public ?string $untouched;
}
