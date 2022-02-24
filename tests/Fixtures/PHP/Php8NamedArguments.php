<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

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
