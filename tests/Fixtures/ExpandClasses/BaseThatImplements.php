<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ExpandClasses;

/**
 * @OA\Schema()
 */
class BaseThatImplements implements BaseInterface
{

    /**
     * @OA\Property();
     * @var string
     */
    public $baseProperty;

    /**
     * {@inheritDoc}
     */
    public function getInterfaceProperty()
    {
        return "foo";
    }
}
