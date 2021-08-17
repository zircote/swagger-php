<?php declare(strict_types=1);

namespace AnotherNamespace;

use OpenApi\Tests\Fixtures\ExpandClasses\AncestorWithoutDocBlocks;

/**
 * @OA\Schema()
 */
class ChildWithDocBlocks extends AncestorWithoutDocBlocks
{

    /**
     * @var bool
     * @OA\Property()
     */
    public $isBaby;
}
