<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace AnotherNamespace;

use OpenApi\Tests\Fixtures\ExpandClasses\Ancestor;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class Child extends Ancestor
{
    /**
     * @var bool
     */
    #[OAT\Property]
    public $isBaby;
}
