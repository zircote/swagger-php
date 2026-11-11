<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP\GroupedImports;

use OpenApi\Tests\Fixtures\PHP\Inheritance\{
    BaseClass as ParentClass,
    BaseInterface as Contract,
    BaseTrait as Shared,
    function helper,
    const FLAG
};
use function OpenApi\Tests\Fixtures\PHP\Inheritance\{otherHelper, anotherHelper};
use const OpenApi\Tests\Fixtures\PHP\Inheritance\{OTHER_FLAG, ANOTHER_FLAG};

class MixedKinds extends ParentClass implements Contract
{
    use Shared;
}
