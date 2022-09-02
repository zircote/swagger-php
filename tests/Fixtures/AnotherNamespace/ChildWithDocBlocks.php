<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace AnotherNamespace;

use OpenApi\Tests\Fixtures\ExpandClasses\AncestorWithoutDocBlocks;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema
 */
class ChildWithDocBlocks extends AncestorWithoutDocBlocks
{
    /**
     * @var bool
     *
     * @OA\Property
     */
    public $isBaby;
}
