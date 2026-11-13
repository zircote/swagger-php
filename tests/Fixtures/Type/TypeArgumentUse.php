<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Type;

/**
 * Uses of a class with type arguments.
 */
final class TypeArgumentUse
{
    /**
     * @param Template<Number, \JsonSerializable>             $single
     * @param Template<int<0, 100>, \JsonSerializable>|null   $nullable
     * @param list<Template<list<Number>, \JsonSerializable>> $items
     * @param array<string, Template<int, \JsonSerializable>> $map
     * @param Template<Number, \JsonSerializable>|Number      $union
     * @param \DateTimeImmutable<int>                         $date
     * @param \ArrayObject<int, Number>                       $collection
     */
    public function __construct(
        public Template $single,
        public ?Template $nullable,
        public array $items,
        public array $map,
        public Template|Number $union,
        public \DateTimeImmutable $date,
        public \ArrayObject $collection,
        public Template $plain,
    ) {
    }
}
