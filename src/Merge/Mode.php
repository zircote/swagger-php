<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Merge;

/**
 * How a folding merger treats two halves that overlap: a field, or a keyed child such as a
 * parameter, that both halves set.
 *
 * What only one half sets is always taken. A merger that accepts a mode takes either a case or a
 * callable that receives both halves and returns one, so the choice can depend on the pair, for
 * example on a mark one producer left with `setMeta()`.
 */
enum Mode
{
    /** Fold into the earlier half: on a field both set differently, the earlier value stays. */
    case First;

    /** Fold into the later half: on a field both set differently, the later value wins. */
    case Last;

    /**
     * Take only what one half has alone: a field or child both set is left as the earlier half
     * has it, and not folded into.
     */
    case NonOverlapping;

    /**
     * How a collision report says what was kept.
     */
    public function outcome(): string
    {
        return match ($this) {
            self::First => 'folding into the first',
            self::Last => 'folding into the last',
            self::NonOverlapping => 'taking only what does not overlap',
        };
    }
}
