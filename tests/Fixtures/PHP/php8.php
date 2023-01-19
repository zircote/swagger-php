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

#[\Attribute]
class ReservedWordsAttr
{
    public function __construct(
        $abstract = null,
        $namespace = null,
        $use = null,
        $class = null,
        $interface = null,
        $extends = null,
        $implements = null,
        $function = null
    ) {
    }
}

#[ReservedWordsAttr(
    abstract: 'example',            // No space
    namespace : 'example',          // One space
    use /* comment */ : 'example',  // Comment
    class : 'example',
    interface : 'example',
    extends : 'example',
    implements : 'example',
    function : 'example'
)]
class UserlandClass
{
}
