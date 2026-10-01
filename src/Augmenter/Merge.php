<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\MergerInterface;
use OpenApi\Specification;
use OpenApi\Utils\PipeInterface;
use OpenApi\Utils\TypedList;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Reduces every root collection to one entry per key, through the registered mergers.
 *
 * Two attributes can claim one key — two operations on the same path and method, two schemas
 * named `Pet`. The merger that claims the type says what identifies it and what the survivor is,
 * and one entry per key reaches the compiler.
 *
 * Only the root collections, because that is where the halves come from different places — a
 * scan, a `withSpecification()` hook, the resolver, an inheritance clone — and something has to
 * decide between them. A collision *inside* one attribute is two entries the same author wrote
 * in one place; the compiler reports it and keeps its own last-write-wins.
 *
 * Registered twice, and both are this class. The first run is the first pipe of the **reduce**
 * phase, which is the earliest point every identity exists — `Augmenter\PathItems` resolves an
 * operation's path and `Augmenter\Names` infers component keys, both in **resolve** — and it is
 * before `Cleanup` and everything downstream that should see the survivor rather than both
 * halves. The second is the last pipe of all, so what a late augmenter adds is reduced too;
 * it is a grouping over lists and costs nothing when there is nothing to do. An augmenter
 * registered after it runs after it; `insert()` is how to land ahead.
 *
 * @implements PipeInterface<Specification>
 */
class Merge implements PipeInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param TypedList<MergerInterface> $mergers
     */
    public function __construct(
        protected TypedList $mergers,
        protected Group $group = Group::Reduce,
    ) {
    }

    public function __invoke(mixed $payload): mixed
    {
        if ($this->logger instanceof LoggerInterface) {
            foreach ($this->mergers as $merger) {
                if ($merger instanceof LoggerAwareInterface) {
                    $merger->setLogger($this->logger);
                }
            }
        }

        // every root collection, discovered rather than listed: a new bucket on the
        // `Specification` is reduced without this pass being told about it
        foreach (get_object_vars($payload) as $property => $value) {
            if (is_array($value)) {
                $payload->{$property} = $this->reduce($value);
            }
        }

        return null;
    }

    public function group(): string|\BackedEnum
    {
        return $this->group;
    }

    /**
     * @param  array<mixed> $attributes
     * @return list<mixed>
     */
    protected function reduce(array $attributes): array
    {
        if (count($attributes) < 2) {
            return array_values($attributes);
        }

        $reduced = [];
        $slots = [];

        foreach ($attributes as $attribute) {
            $merger = $attribute instanceof AttributeInterface ? $this->mergerFor($attribute) : null;
            $identity = $merger?->identity($attribute);

            if (!$merger instanceof MergerInterface || $identity === null) {
                $reduced[] = $attribute;
                continue;
            }

            // two mergers never fold into each other: whoever claimed the type decides
            $key = $merger::class . "\0" . $identity;

            if (!array_key_exists($key, $slots)) {
                $slots[$key] = count($reduced);
                $reduced[] = $attribute;
                continue;
            }

            $reduced[$slots[$key]] = $merger->merge($reduced[$slots[$key]], $attribute);
        }

        return $reduced;
    }

    protected function mergerFor(AttributeInterface $attribute): ?MergerInterface
    {
        foreach ($this->mergers as $merger) {
            if ($merger->supports($attribute::class)) {
                return $merger;
            }
        }

        return null;
    }
}
