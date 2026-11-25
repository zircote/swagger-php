<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

/**
 * A class described as a component that is a collection too, as a Laravel resource is.
 *
 * @implements \ArrayAccess<string, mixed>
 */
#[OA\Schema(component: 'CollectionItem', properties: [new OA\Property(property: 'id', schema: new OA\Schema(type: 'integer'))])]
class CollectionItem implements \ArrayAccess
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
class CollectionBag implements \IteratorAggregate
{
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator([]);
    }
}

#[OA\Schema(component: 'CollectionClasses')]
class CollectionClasses
{
    /** @var CollectionItem[] */
    #[OA\Property]
    public array $brackets = [];

    /** @var list<CollectionItem> */
    #[OA\Property]
    public array $list = [];

    /** @var CollectionItem */
    #[OA\Property]
    public $one;

    /** @var CollectionItem|null */
    #[OA\Property]
    public $maybe;

    /** @var CollectionBag */
    #[OA\Property]
    public $bag;

    /** @var CollectionBag|null */
    #[OA\Property]
    public $maybeBag;

    /** @var CollectionBag<int, CollectionItem> */
    #[OA\Property]
    public $typed;

    /** @var int|CollectionBag */
    #[OA\Property]
    public $either;
}
