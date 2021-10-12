<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ExpandClasses;

/**
 * @OA\Schema()
 */
class ExtendedWithoutAllOf extends Base
{

    /**
     * @OA\Property();
     * @var string
     */
    public $extendedProperty;
}
