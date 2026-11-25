<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Attributes as OA;

/**
 * Classic attributes on the classes of `CollectionClasses`, a class described as a component
 * that is a collection too, as a Laravel resource is.
 *
 * @implements \ArrayAccess<string, mixed>
 */
#[OA\Schema(schema: 'ClassicCollectionItem', properties: [new OA\Property(property: 'id', type: 'integer')])]
class ClassicCollectionItem implements \ArrayAccess
{
    public function offsetExists(mixed $offset): bool
    {
        return false;
    }

    public function offsetGet(mixed $offset): mixed
    {
        return null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }
}

/**
 * A collection nothing describes.
 *
 * @implements \IteratorAggregate<int, mixed>
 */
class ClassicCollectionBag implements \IteratorAggregate
{
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator([]);
    }
}

#[OA\Schema(schema: 'ClassicCollectionClasses')]
class ClassicCollectionClasses
{
    /** @var ClassicCollectionItem[] */
    #[OA\Property]
    public array $brackets = [];

    /** @var list<ClassicCollectionItem> */
    #[OA\Property]
    public array $list = [];

    /** @var ClassicCollectionItem */
    #[OA\Property]
    public $one;

    /** @var ClassicCollectionItem|null */
    #[OA\Property]
    public $maybe;

    /** @var ClassicCollectionBag */
    #[OA\Property]
    public $bag;

    /** @var ClassicCollectionBag|null */
    #[OA\Property]
    public $maybeBag;

    /** @var ClassicCollectionBag<int, ClassicCollectionItem> */
    #[OA\Property]
    public $typed;

    /** @var int|ClassicCollectionBag */
    #[OA\Property]
    public $either;
}
