<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Type;

/**
 * A class template parameter in a constructor's docblock.
 *
 * @template T
 * @template B of \JsonSerializable
 */
final class Template
{
    /**
     * @param list<T>                 $results
     * @param list<B>                 $bounded
     * @param T|int                   $either
     * @param B&\Countable            $counted
     * @param T&\Countable            $unbounded
     * @param array{item: T, id: int} $shaped
     */
    public function __construct(
        public array $results,
        public array $bounded,
        public mixed $either = null,
        public mixed $counted = null,
        public mixed $unbounded = null,
        public array $shaped = [],
    ) {
    }

    /**
     * A method's own template, hiding the class's.
     *
     * @template T
     *
     * @param T $value
     *
     * @return T
     */
    public function same(mixed $value): mixed
    {
        return $value;
    }
}
