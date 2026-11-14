<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Type;

/**
 * A generic class.
 *
 * @template T
 */
class Box
{
    /** @var T */
    public mixed $value;

    /** @var int */
    public mixed $count;
}
