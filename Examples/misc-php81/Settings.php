<?php declare(strict_types=1);

namespace OpenApi\Examples\MiscPhp81;

use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema]
class Settings
{
    #[Property]
    public bool $doThings;
}
