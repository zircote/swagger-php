<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Merge\Concerns;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification\ComponentKey;
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
    /**
     * What makes two attributes the same one depends on the collection they are in:
     *
     * - anything in a `components` bucket: its component name
     * - an operation: its path and method, or its webhook and method
     * - a path item outside `components`: its path
     * - a tag: its name
     *
     * Anything else has no identity and passes through. Servers and external documentation are
     * positional, so duplicates there are legal and stay.
     */
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

            // a path item without a component name is path-bound, and keyed by its path
            return $attribute instanceof OA\PathItem && $attribute->path !== null
                ? 'path:' . $attribute->path
                : null;
        }

        return null;
    }

    /**
     * Both are named, because either could be the one the author did not mean to write. Two
     * contributed attributes both report `unknown`, which is stated once.
     *
     * Two components with different keys describe different things, so the name was most likely
     * derived twice rather than written twice, and the message says how to tell them apart.
     *
     * `$detail` goes before the locations, which end the message.
     */
    protected function collision(AttributeInterface $earlier, AttributeInterface $later, string $outcome = 'the last one kept', string $detail = ''): string
    {
        $earlierAt = (string) $earlier->getSourceLocation();
        $laterAt = (string) $later->getSourceLocation();
        $earlierKey = $earlier->getComponentKey();
        $laterKey = $later->getComponentKey();
        $split = ComponentName::isComponentType($later)
            && $earlierKey instanceof ComponentKey && $laterKey instanceof ComponentKey
            && !$earlierKey->equals($laterKey);

        if ($split) {
            $detail .= ($detail !== '' ? '; ' : '') . sprintf('they describe %s and %s, give one a name of its own or name components with QualifiedName', $earlierKey, $laterKey);
        }

        return sprintf(
            '%s "%s" is declared more than once, %s%s in %s',
            (new \ReflectionClass($later))->getShortName(),
            $this->label($later),
            $outcome,
            $detail !== '' ? '; ' . $detail : '',
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
