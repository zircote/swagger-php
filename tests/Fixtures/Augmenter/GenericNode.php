<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

/**
 * A generic class using itself with a growing argument, for the depth limit.
 *
 * @template T
 */
class GenericNode
{
    /** @var GenericNode<list<T>>|null */
    public ?GenericNode $next = null;
}
