<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace AnotherNamespace;

use OpenApi\Tests\Fixtures\ExpandClasses\AncestorWithoutDocBlocks;
use OpenApi\Attributes as OAT;

#[OAT\Schema]
class ChildWithDocBlocks extends AncestorWithoutDocBlocks
{
    /**
     * @var bool
     */
    #[OAT\Property]
    public $isBaby;
}
