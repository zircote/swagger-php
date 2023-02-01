<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema
 */
class Php8NamedArguments
{
    public function useFoo(): void
    {
        $this->foo(class: 'abc', interface: 'def', trait: 'xyz');
    }

    public function foo(string $class, string $interface, string $trait): void
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
