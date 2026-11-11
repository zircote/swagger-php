<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP\GroupedImports;

use OpenApi\Tests\Fixtures\PHP\Inheritance\{BaseClass as ParentClass, BaseInterface as Contract, BaseTrait as Shared};

class Inline extends ParentClass implements Contract
{
    use Shared;
}
