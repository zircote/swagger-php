<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Contracts;

/**
 * What makes two attributes the same one, and what one of them becomes.
 *
 * Mergers are a chain, first match wins, the shape {@see ResolverInterface} already has:
 * `supports()` is asked in registration order, so a merger claiming a specific type goes
 * before one claiming everything. `Merge\LastWins` ships as the catch-all and is registered
 * last for that reason.
 *
 * Identity and fold live together on purpose. "Two operations on the same path and method are
 * the same operation" and "this is what the surviving one looks like" are one decision, and
 * keeping them on one object means a consumer that keys its own type differently changes both
 * in one place.
 *
 * The core has no precedence opinion beyond producer order. Scanned beats contributed, or the
 * reverse, is a merger a consumer registers. A producer that needs to recognise its own
 * attributes later marks them with `setMeta()` and reads that back in `merge()`.
 */
interface MergerInterface
{
    /**
     * @param class-string $class
     */
    public function supports(string $class): bool;

    /**
     * What makes two of these the same, as a string, or null when this attribute never merges
     * and passes through untouched.
     *
     * The string is a grouping key, compared only against others from the same merger, so it
     * only has to distinguish attributes from one another — not read well.
     */
    public function identity(AttributeInterface $attribute): ?string;

    /**
     * Fold two attributes with the same identity into the one that survives.
     *
     * `$earlier` precedes `$later` in producer order, which is the only guarantee the pipeline
     * makes and the one fact a precedence policy reads: `return $later` is last-wins. Folding
     * a group of three calls this twice, so on the second call `$earlier` is the survivor of
     * the first two and neither half is "the first" any more; producer order still holds.
     *
     * The survivor may be either argument or a new attribute.
     */
    public function merge(AttributeInterface $earlier, AttributeInterface $later): AttributeInterface;
}
