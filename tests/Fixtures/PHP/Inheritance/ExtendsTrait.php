<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP\Inheritance;

trait ExtendsTrait
{
    use BaseTrait;

    public $extendsTraitProp;

    public function extendsTraitFunc()
    {

    }
}
