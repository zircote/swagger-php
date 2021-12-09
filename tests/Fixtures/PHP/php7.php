<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use PHPUnit\Framework\TestCase;

$a = new class {
    public function foo()
    {
    }
};

$b = new class() {
    public function bar()
    {
    }
};

$c = new class extends \stdClass {
    public function baz()
    {
    }
};

$d = new class() extends \stdClass {
    public function boz()
    {
    }
};

new class implements i1 {
    public function biz()
    {
    }
};

new class() implements i1 {
    public function buz()
    {
    }
};

$e = new class() extends \stdClass {
    public function fuz()
    {
    }
};

$f = new class() implements i2 {
    public function fuu()
    {
    }
};

function deng()
{
}

$foo = TestCase::class;
