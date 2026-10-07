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
 * and its own accidental rules stop deciding anything. Combining fields from both halves is a
 * type-specific merger's job, registered ahead of this one.
 *
 * Identity by collection: the component key for anything in a `components` bucket, path and
 * method for an operation, webhook and method for a webhook operation, path for a path-bound
 * path item, name for a tag. Servers, security requirements and external documentation are
 * positional — their entries have no identity, duplicates are legal, and they pass through.
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
