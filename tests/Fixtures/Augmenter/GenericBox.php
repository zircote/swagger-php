<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

/**
 * A generic class, for the members a copy is made and typed from.
 *
 * @template T
 */
class GenericBox
{
    /** @var T */
    public mixed $value;

    /** @var int */
    public mixed $count;
}
