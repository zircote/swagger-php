<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

/**
 * A generic class with members of its own, inherited ones and one taken from a trait.
 *
 * @template T
 *
 * @extends GenericBox<T>
 */
class GenericStampedBox extends GenericBox
{
    /** @use GenericBoxStamp<T> */
    use GenericBoxStamp;

    /** @var T */
    public mixed $own;
}
