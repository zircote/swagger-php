<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Merge;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\MergerInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification\ComponentName;
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
    use LoggerAwareTrait;

    public function supports(string $class): bool
    {
        return true;
    }

    public function identity(AttributeInterface $attribute): ?string
    {
        if ($attribute instanceof OA\Operation) {
            if ($attribute->method === null) {
                return null;
            }

            return match (true) {
                $attribute->path !== null => 'operation:' . $attribute->method . ' ' . $attribute->path,
                $attribute->webhook !== null => 'webhook:' . $attribute->method . ' ' . $attribute->webhook,
                default => null,
            };
        }

        if ($attribute instanceof OA\Tag) {
            return $attribute->name !== null ? 'tag:' . $attribute->name : null;
        }

        if (ComponentName::isComponentType($attribute)) {
            $component = ComponentName::of($attribute);
            if ($component !== null) {
                return 'component:' . $component;
            }

            // a path item without a component key is path-bound, and keyed by its path
            return $attribute instanceof OA\PathItem && $attribute->path !== null
                ? 'path:' . $attribute->path
                : null;
        }

        return null;
    }

    public function merge(AttributeInterface $earlier, AttributeInterface $later): AttributeInterface
    {
        $this->logger?->warning($this->collision($earlier, $later));

        return $later;
    }

    /**
     * Both halves are named, because either could be the one the author did not mean to write.
     * Two contributed halves both report `unknown`, which is stated once.
     */
    protected function collision(AttributeInterface $earlier, AttributeInterface $later): string
    {
        $earlierAt = (string) $earlier->getSourceLocation();
        $laterAt = (string) $later->getSourceLocation();

        return sprintf(
            '%s "%s" is declared more than once, keeping the last in %s',
            (new \ReflectionClass($later))->getShortName(),
            $this->label($later),
            $earlierAt === $laterAt ? $laterAt : $laterAt . ' and ' . $earlierAt,
        );
    }

    /**
     * What the key reads as in a message. The identity string is a grouping key and carries a
     * space prefix nobody needs to see.
     */
    protected function label(AttributeInterface $attribute): string
    {
        if ($attribute instanceof OA\Operation) {
            return trim(($attribute->method ?? '') . ' ' . ($attribute->path ?? $attribute->webhook ?? ''));
        }

        if ($attribute instanceof OA\Tag) {
            return (string) $attribute->name;
        }

        return (string) (ComponentName::of($attribute) ?? ($attribute instanceof OA\PathItem ? $attribute->path : null));
    }
}
