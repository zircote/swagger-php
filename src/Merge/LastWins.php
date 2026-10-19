<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Merge;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\MergerInterface;
use OpenApi\Merge\Concerns\IdentityTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * The catch-all merger: one entry per key survives, the later one, and the author is told.
 *
 * It reduces rather than folds: two entries in, one out, so the compiler never sees a collision
 * and its own accidental rules stop deciding anything. Combining fields from both attributes is a
 * type-specific merger's job, registered ahead of this one.
 *
 * It claims every type, and keys each by the identity all shipped mergers share.
 */
class LastWins implements MergerInterface, LoggerAwareInterface
{
    use IdentityTrait;
    use LoggerAwareTrait;

    public function supports(string $class): bool
    {
        return true;
    }

    public function merge(AttributeInterface $earlier, AttributeInterface $later): AttributeInterface
    {
        $this->logger?->warning($this->collision($earlier, $later));

        return $later;
    }
}
