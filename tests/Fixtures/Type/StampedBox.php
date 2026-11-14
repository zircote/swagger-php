<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Type;

/**
 * A generic class with a member of its own, inherited ones and one taken from a trait.
 *
 * @template T
 *
 * @extends Box<T>
 */
class StampedBox extends Box
{
    /** @use BoxStamp<T> */
    use BoxStamp;

    /** @var T */
    public mixed $own;
}
