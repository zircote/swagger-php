<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Specification;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;

/**
 * The key a component is filed under in its bucket.
 *
 * The compiler and `ComponentIndex` both need this, and used to answer it separately. Their
 * answers had drifted apart: a link with no `link` compiled under its `operationId` while the
 * index recorded no name at all, so the component shipped under a key no `$ref` could reach.
 * Asking one place makes "compiled as X" and "resolvable as X" the same statement.
 *
 * Each component type belongs to exactly one bucket, so the type is enough to identify the
 * field — no bucket has to be passed in and mismatched.
 */
class ComponentName
{
    /**
     * Every `components` bucket, named as both the `Specification` property holding it and the
     * segment a `$ref` to it carries. `pathItems` doubles as the `paths` source: a `PathItem`
     * without a key is path-bound and is not a component.
     */
    public const BUCKETS = ['schemas', 'responses', 'parameters', 'requestBodies', 'headers', 'securitySchemes', 'links', 'examples', 'pathItems', 'mediaTypes'];

    /**
     * The key, or null when the component has none and cannot be referenced.
     *
     * One field on every type, `component`. The historic per-type spellings — `schema`,
     * `parameter`, `request`, `securityScheme` — alias onto it in their constructors. The
     * ones that are also a nesting key — `response`, `header`, `link`, `example` — cannot:
     * the object does not know at construction whether it is nested. This is only ever asked
     * of an item in a root bucket, where it is not nested, so here the historic key *is* the
     * component key and is read as a fallback. `normalise()` writes the same answer into
     * `component` once per build, which is where the spelling is reported as deprecated.
     */
    public static function of(AttributeInterface $item): ?string
    {
        if (!self::isComponentType($item)) {
            return null;
        }

        return $item->component ?? self::inferred($item)[1];
    }

    /**
     * The constructor field a missing key is reported against, for diagnostics.
     */
    public static function keyField(AttributeInterface $item): ?string
    {
        return self::of($item) === null && !self::isComponentType($item) ? null : 'component';
    }

    /**
     * Whether the type can carry a component key at all.
     */
    public static function isComponentType(AttributeInterface $item): bool
    {
        return $item instanceof OA\Schema
            || $item instanceof OA\Response
            || $item instanceof OA\Parameter
            || $item instanceof OA\RequestBody
            || $item instanceof OA\Header
            || $item instanceof OA\Security\Scheme
            || $item instanceof OA\Link
            || $item instanceof OA\Example
            || $item instanceof OA\PathItem
            || $item instanceof OA\MediaType;
    }

    /**
     * Fill `component` on every root component that still spells its key the historic way.
     *
     * `response`, `header`, `link` and `example` are the nesting key when the attribute is
     * nested and the component key when it is not, and the object cannot tell the two apart
     * at construction — nesting is structural, and a nested attribute never reaches a root
     * slot. Here it has: anything in a root bucket is a component, so its historic key is the
     * component key and aliases across. The one inference that reads a value field —
     * `Parameter::$name` — lives here for the same reason.
     *
     * Runs before the resolver builds the first `ComponentIndex`, and again after the hybrid
     * bridge adds classic-derived attributes; only the first pass reports the spelling as
     * deprecated, since the bridge's callers never wrote it. A `Specification` that never
     * passes through here — hand-built and compiled directly — still keys correctly, because
     * `of()` reads the same fallback; it only misses the deprecation notice.
     */
    public static function normalise(Specification $specification, bool $deprecate): void
    {
        foreach (self::BUCKETS as $bucket) {
            foreach ($specification->{$bucket} as $item) {
                if (!self::isComponentType($item) || $item->component !== null) {
                    continue;
                }

                [$field, $value] = self::inferred($item);
                if ($value === null) {
                    continue;
                }

                if ($field !== null && $deprecate) {
                    trigger_deprecation('zircote/swagger-php', '6.12', '`%s` is deprecated as the component key of %s and will be removed in 8.0; use `component`', $field, $item::class);
                }

                $item->component = $value;
            }
        }
    }

    /**
     * What `component` falls back to: the historic spelling for the four nesting-key types,
     * and the one inference that reads a value field — a parameter is keyed by its name. The
     * field name is reported when the spelling is deprecated, and is null for the inference,
     * which is not. A schema used to fall back to its title; nothing relied on it, and a
     * keyless schema is better reported than silently named.
     *
     * @return array{string|null, string|null} [field, value]
     */
    protected static function inferred(AttributeInterface $item): array
    {
        return match (true) {
            $item instanceof OA\Response => ['response', $item->response === null ? null : (string) $item->response],
            $item instanceof OA\Header => ['header', $item->header],
            $item instanceof OA\Link => ['link', $item->link],
            $item instanceof OA\Example => ['example', $item->example],
            $item instanceof OA\Parameter => [null, $item->name],
            default => [null, null],
        };
    }
}
