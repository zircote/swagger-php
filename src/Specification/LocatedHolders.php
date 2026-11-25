<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Specification;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Specification;

/**
 * The nearest attribute with a location of its own that holds each attribute without one.
 *
 * A nested attribute is built inside another one's arguments and has no location of its own, so a
 * message about it names where its holder was declared. An attribute reachable from two places
 * takes the holder found first.
 *
 * The map is built once, from the specification as it is then; an attribute added later has no holder.
 */
class LocatedHolders
{
    /**
     * @var \SplObjectStorage<AttributeInterface, AttributeInterface>
     */
    protected \SplObjectStorage $holders;

    public function __construct(Specification $specification)
    {
        $this->holders = new \SplObjectStorage();
        $seen = new \SplObjectStorage();

        $descend = function (mixed $value, ?AttributeInterface $holder) use (&$descend, $seen): void {
            foreach (is_array($value) ? $value : [$value] as $item) {
                if (is_array($item)) {
                    $descend($item, $holder);
                    continue;
                }

                if (!$item instanceof AttributeInterface || $seen->offsetExists($item)) {
                    continue;
                }
                $seen->offsetSet($item);

                $located = $item->getReflector() instanceof \Reflector;
                if (!$located && $holder instanceof AttributeInterface) {
                    $this->holders->offsetSet($item, $holder);
                }

                foreach (get_object_vars($item) as $nested) {
                    $descend($nested, $located ? $item : $holder);
                }
            }
        };

        foreach (get_object_vars($specification) as $bucket) {
            $descend($bucket, null);
        }
    }

    /**
     * The attribute itself where it has a location, otherwise the nearest one holding it that has.
     *
     * An attribute with no located holder is returned as it is.
     */
    public function locatedFor(AttributeInterface $attribute): AttributeInterface
    {
        if ($attribute->getReflector() instanceof \Reflector || !$this->holders->offsetExists($attribute)) {
            return $attribute;
        }

        return $this->holders->offsetGet($attribute);
    }

    /**
     * Where the attribute, or the nearest one holding it, was declared, as a message's ending.
     *
     * Nothing where neither has a location, such as an attribute built in code.
     */
    public function location(AttributeInterface $attribute): string
    {
        $located = $this->locatedFor($attribute);

        return $located->getReflector() instanceof \Reflector ? ' in ' . $located->getSourceLocation() : '';
    }
}
