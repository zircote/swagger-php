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
     * @param list<T>      $results
     * @param list<B>      $bounded
     * @param T&\Countable $unbounded
     */
    public function __construct(
        public array $results,
        public array $bounded,
        public mixed $unbounded = null,
    ) {
    }
}
