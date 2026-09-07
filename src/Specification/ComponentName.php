<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Specification;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Spec as OA;

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
     * segment a `$ref` to it carries.
     */
    public const BUCKETS = ['schemas', 'responses', 'parameters', 'requestBodies', 'headers', 'securitySchemes', 'links', 'examples'];

    /**
     * The key, or null when the component has none and cannot be referenced.
     */
    public static function of(AttributeInterface $item): ?string
    {
        return match (true) {
            $item instanceof OA\Schema => $item->schema ?? $item->title,
            $item instanceof OA\Response => $item->response !== null ? (string) $item->response : null,
            $item instanceof OA\Parameter => $item->parameter ?? $item->name,
            $item instanceof OA\RequestBody => $item->request,
            $item instanceof OA\Header => $item->header,
            $item instanceof OA\Security\Scheme => $item->securityScheme,
            $item instanceof OA\Link => $item->link,
            $item instanceof OA\Example => $item->example,
            default => null,
        };
    }

    /**
     * The constructor field a missing key is reported against, for diagnostics.
     */
    public static function keyField(AttributeInterface $item): ?string
    {
        return match (true) {
            $item instanceof OA\Schema => 'schema',
            $item instanceof OA\Response => 'response',
            $item instanceof OA\Parameter => 'parameter',
            $item instanceof OA\RequestBody => 'request',
            $item instanceof OA\Header => 'header',
            $item instanceof OA\Security\Scheme => 'securityScheme',
            $item instanceof OA\Link => 'link',
            $item instanceof OA\Example => 'example',
            default => null,
        };
    }
}
