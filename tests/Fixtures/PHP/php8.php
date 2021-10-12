<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

#[\Attribute(\Attribute::TARGET_METHOD)]
class MethodAttr
{
}

#[\Attribute]
class GenericAttr
{
    public function __construct($name = null)
    {

    }
}

#[GenericAttr(name: 'example')]
class Decorated
{

    #[MethodAttr]
    public function foo()
    {
    }

    public function bar(#[GenericAttr] string $ding)
    {
    }
}