<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace AnotherNamespace;

use OpenApi\Attributes as OAT;
use OpenApi\Tests\Fixtures\ExpandClasses\AncestorWithoutDocBlocks;

#[OAT\Schema]
class ChildWithDocBlocks extends AncestorWithoutDocBlocks
{
    /**
     * @var bool
     */
    #[OAT\Property]
    public $isBaby;
}
