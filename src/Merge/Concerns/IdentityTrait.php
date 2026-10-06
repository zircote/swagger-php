<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Merge\Concerns;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification\ComponentName;

/**
 * What makes two attributes the same one, and how a collision between them reads.
 *
 * Shared by the shipped mergers so "the same" is defined once: registering a fold changes how
 * two attributes combine, not which two are the same. A trait rather than a base class, because
 * `TypedList` finds mergers by `instanceof`, and a fold must not answer to the catch-all's name.
 */
trait IdentityTrait
{
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

    /**
     * Both halves are named, because either could be the one the author did not mean to write.
     * Two contributed halves both report `unknown`, which is stated once.
     */
    protected function collision(AttributeInterface $earlier, AttributeInterface $later, string $outcome = 'keeping the last'): string
    {
        $earlierAt = (string) $earlier->getSourceLocation();
        $laterAt = (string) $later->getSourceLocation();

        return sprintf(
            '%s "%s" is declared more than once, %s in %s',
            (new \ReflectionClass($later))->getShortName(),
            $this->label($later),
            $outcome,
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
