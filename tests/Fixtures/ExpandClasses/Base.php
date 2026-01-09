<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ExpandClasses;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class Base
{
    #[OAT\Property]
    /**
     * @var string
     */
    public $baseProperty;
}
