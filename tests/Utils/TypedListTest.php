<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Utils;

use OpenApi\OpenApiException;
use OpenApi\Utils\TypedList;
use PHPUnit\Framework\TestCase;

final class TypedListTest extends TestCase
{
    public function testConstructWithItems(): void
    {
        $list = new TypedList(['a', 'b', 'c']);

        $this->assertSame(['a', 'b', 'c'], $this->items($list));
    }

    public function testAdd(): void
    {
        $list = new TypedList();
        $list->add('a');
        $list->add('b');

        $this->assertSame(['a', 'b'], $this->items($list));
    }

    public function testAddReturnsSelf(): void
    {
        $list = new TypedList();

        $this->assertSame($list, $list->add('x'));
    }

    public function testCount(): void
    {
        $list = new TypedList(['a', 'b', 'c']);

        $this->assertSame(3, $list->count());
    }

    public function testCountEmpty(): void
    {
        $this->assertSame(0, (new TypedList())->count());
    }

    public function testGetIterator(): void
    {
        $list = new TypedList(['a', 'b']);

        $this->assertInstanceOf(\Traversable::class, $list->getIterator());
        $this->assertSame(['a', 'b'], iterator_to_array($list));
    }

    public function testRemoveByInstance(): void
    {
        $a = new \stdClass();
        $b = new \stdClass();
        $list = new TypedList([$a, $b]);

        $list->remove($a);

        $this->assertSame([$b], $this->items($list));
    }

    public function testRemoveByMatcher(): void
    {
        $keep = new TypedListTestItem('keep');
        $remove = new TypedListTestItem('remove');
        $list = new TypedList([$keep, $remove]);

        $list->remove(null, fn (TypedListTestItem $item): bool => $item->name !== 'remove');

        $this->assertSame([$keep], $this->items($list));
    }

    public function testRemoveByClassString(): void
    {
        $a = new \stdClass();
        $b = new TypedListTestMarkerItem();
        $list = new TypedList([$a, $b]);

        $list->remove(TypedListTestMarkerItem::class);

        $this->assertSame([$a], $this->items($list));
    }

    public function testRemoveNonExistentInstance(): void
    {
        $a = new \stdClass();
        $list = new TypedList([$a]);

        $list->remove(new \stdClass());

        $this->assertSame([$a], $this->items($list));
    }

    public function testRemoveRequiresItemOrMatcher(): void
    {
        $this->expectException(OpenApiException::class);

        (new TypedList())->remove();
    }

    public function testRemoveReturnsSelf(): void
    {
        $a = new \stdClass();
        $list = new TypedList([$a]);

        $this->assertSame($list, $list->remove($a));
    }

    public function testInsertByClassString(): void
    {
        $a = new \stdClass();
        $b = new TypedListTestMarkerItem();
        $list = new TypedList([$a, $b]);

        $c = new \stdClass();
        $list->insert($c, TypedListTestMarkerItem::class);

        $this->assertSame([$a, $c, $b], $this->items($list));
    }

    public function testInsertByMatcher(): void
    {
        $list = new TypedList(['a', 'c']);

        $list->insert('b', fn (array $items): int => 1);

        $this->assertSame(['a', 'b', 'c'], $this->items($list));
    }

    public function testInsertOutOfRangeThrows(): void
    {
        $this->expectException(OpenApiException::class);

        (new TypedList(['a']))->insert('b', fn (array $items): int => -1);
    }

    public function testInsertReturnsSelf(): void
    {
        $list = new TypedList(['a']);

        $this->assertSame($list, $list->insert('b', fn (array $items): int => 0));
    }

    public function testWalk(): void
    {
        $list = new TypedList(['a', 'b']);

        $collected = [];
        $list->walk(function (string $item) use (&$collected): void {
            $collected[] = $item;
        });

        $this->assertSame(['a', 'b'], $collected);
    }

    public function testWalkReturnsSelf(): void
    {
        $list = new TypedList(['a']);

        $this->assertSame($list, $list->walk(function (): void {
        }));
    }

    public function testGet(): void
    {
        $marker = new TypedListTestMarkerItem();
        $list = new TypedList([new \stdClass(), $marker]);

        $this->assertSame($marker, $list->get(TypedListTestMarkerItem::class));
    }

    public function testGetReturnsNullForMissing(): void
    {
        $list = new TypedList([new \stdClass()]);

        $this->assertNotInstanceOf(TypedListTestMarkerItem::class, $list->get(TypedListTestMarkerItem::class));
    }

    public function testClear(): void
    {
        $list = new TypedList(['a', 'b', 'c']);

        $list->clear();

        $this->assertSame(0, $list->count());
        $this->assertSame([], $this->items($list));
    }

    public function testClearReturnsSelf(): void
    {
        $list = new TypedList();

        $this->assertSame($list, $list->clear());
    }

    /**
     * @return list<mixed>
     */
    protected function items(TypedList $list): array
    {
        return iterator_to_array($list);
    }
}

class TypedListTestItem
{
    public function __construct(public readonly string $name)
    {
    }
}

class TypedListTestMarkerItem
{
}
