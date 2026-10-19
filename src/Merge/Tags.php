<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Merge;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\MergerInterface;
use OpenApi\Merge\Concerns\FoldTrait;
use OpenApi\Merge\Concerns\IdentityTrait;
use OpenApi\Merge\Concerns\ModeTrait;
use OpenApi\Spec as OA;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Folds two tags with the same name field by field, instead of keeping one of them whole.
 *
 * One of the Builder's default mergers, registered ahead of the catch-all: a tag is declared
 * once with its description and then named on every method or class that uses it, and each of
 * those is a declaration too.
 *
 * What only one of them sets is always taken, so a tag that is only a name adds nothing and is
 * not reported. What both set differently is decided by the `Mode` it is constructed with,
 * `Mode::Last` by default, and reported. `NonOverlapping` keeps the earlier's value, as `First`
 * does. `attachables` is a union, and `x` merges key by key under the same mode.
 *
 * The survivor is a new tag, a copy of the earlier with the later folded in. It keeps the
 * earlier's reflector, source location and `meta`.
 */
class Tags implements MergerInterface, LoggerAwareInterface
{
    use FoldTrait;
    use IdentityTrait;
    use LoggerAwareTrait;
    use ModeTrait;

    /**
     * @param Mode|callable(AttributeInterface, AttributeInterface): Mode $mode
     */
    public function __construct(Mode|callable $mode = Mode::Last)
    {
        $this->setMode($mode);
    }

    public function supports(string $class): bool
    {
        return is_a($class, OA\Tag::class, true);
    }

    public function merge(AttributeInterface $earlier, AttributeInterface $later): AttributeInterface
    {
        // supports() claims tags only; anything else is kept as the catch-all would keep it
        if (!$earlier instanceof OA\Tag || !$later instanceof OA\Tag) {
            $this->logger?->warning($this->collision($earlier, $later));

            return $later;
        }

        $mode = $this->modeFor($earlier, $later);

        // a shallow copy is enough: both tags give way to the survivor, so what it shares has one holder
        $survivor = clone $earlier;
        $conflicts = [];

        $this->fold($survivor, $later, ['summary', 'description', 'externalDocs', 'parent', 'kind'], $mode, $conflicts);

        $survivor->attachables = $this->union($survivor->attachables, $later->attachables);
        $survivor->x = $this->extensions($survivor->x, $later->x, $mode, $conflicts);

        if ($conflicts !== []) {
            $this->logger?->warning(sprintf(
                '%s; %s set differently',
                $this->collision($earlier, $later, $mode->outcome()),
                implode(', ', array_unique($conflicts)),
            ));
        }

        return $survivor;
    }
}
