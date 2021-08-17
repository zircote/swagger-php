<?php declare(strict_types=1);

namespace AnotherNamespace;

use OpenApi\Tests\Fixtures\ExpandClasses\Ancestor;

/**
 * @OA\Schema()
 */
class Child extends Ancestor
{

    /**
     * @var bool
     * @OA\Property()
     */
    public $isBaby;
}
