<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ExpandClasses;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema
 */
class Base
{
    /**
     * @OA\Property
     *
     * @var string
     */
    public $baseProperty;
}
