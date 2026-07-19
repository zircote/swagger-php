<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

use OpenApi\OpenApiException;

/**
 * @template T
 */
class TypedList
{
    /**
     * @var list<T>
     */
    protected array $items = [];

    /**
     * @param list<T> $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @param T $item
     */
    public function add(mixed $item): static
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * @template P of object
     *
     * @param class-string<P> $class
     *
     * @return P|null
     */
    public function get(string $class): mixed
    {
        foreach ($this->items as $item) {
            if ($item instanceof $class) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param T|class-string|null $item
     */
    public function remove(mixed $item = null, ?callable $matcher = null): static
    {
        if (!$item && !$matcher) {
            throw new OpenApiException('item or callable must not be empty');
        }

        if (is_string($item) && !$matcher) {
            $itemClass = $item;
            $matcher = (static fn ($item): bool => !$item instanceof $itemClass);
        }

        if ($matcher) {
            $tmp = [];
            foreach ($this->items as $item) {
                if ($matcher($item)) {
                    $tmp[] = $item;
                }
            }

            $this->items = $tmp;
        } else {
            if (false === ($key = array_search($item, $this->items, true))) {
                return $this;
            }

            unset($this->items[$key]);

            $this->items = array_values($this->items);
        }

        return $this;
    }

    /**
     * Insert an item into the list at a specific position determined by a matcher.
     *
     * @param T                     $item
     * @param callable|class-string $matcher either an <code>int</code> from a callable or, in the case of <code>$matcher</code> being
     *                                       a <code>class-string</code>, the position before the first item of that class
     */
    public function insert(mixed $item, callable|string $matcher): static
    {
        if (is_string($matcher)) {
            $before = $matcher;
            $matcher = static function (array $items) use ($before): int|string|null {
                foreach ($items as $ii => $current) {
                    if ($current instanceof $before) {
                        return $ii;
                    }
                }

                return null;
            };
        }

        $index = $matcher($this->items);
        if (null === $index || $index < 0 || $index > count($this->items)) {
            throw new OpenApiException('Matcher result out of range');
        }

        array_splice($this->items, $index, 0, [$item]);

        return $this;
    }

    /**
     * @param callable(T): void $walker
     */
    public function walk(callable $walker): static
    {
        foreach ($this->items as $item) {
            $walker($item);
        }

        return $this;
    }
}
