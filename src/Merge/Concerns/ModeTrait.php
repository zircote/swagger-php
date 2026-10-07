<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Merge\Concerns;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Merge\Mode;

/**
 * A merger's `Mode`, fixed or chosen per pair by a callable that receives both halves.
 */
trait ModeTrait
{
    protected Mode|\Closure $mode = Mode::Last;

    /**
     * How two halves that overlap are treated: a `Mode`, or a callable that receives the earlier
     * and the later half and returns one.
     *
     * @param Mode|callable(AttributeInterface, AttributeInterface): Mode $mode
     */
    public function setMode(Mode|callable $mode): static
    {
        $this->mode = $mode instanceof Mode ? $mode : \Closure::fromCallable($mode);

        return $this;
    }

    protected function modeFor(AttributeInterface $earlier, AttributeInterface $later): Mode
    {
        if ($this->mode instanceof Mode) {
            return $this->mode;
        }

        $mode = ($this->mode)($earlier, $later);
        if (!$mode instanceof Mode) {
            throw new \UnexpectedValueException(sprintf('The mode callable of %s must return a %s, got %s', static::class, Mode::class, get_debug_type($mode)));
        }

        return $mode;
    }
}
