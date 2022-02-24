<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP\Inheritance;

class BaseClass implements BaseInterface
{
    use BaseTrait;

    public $baseClassProp;

    public function baseClassFunc()
    {
    }
}
