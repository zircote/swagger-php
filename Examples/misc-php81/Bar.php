<?php declare(strict_types=1);

namespace OpenApi\Examples\MiscPhp81;

use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema]
class Bar
{
    #[Property]
    public string $foo;
}
