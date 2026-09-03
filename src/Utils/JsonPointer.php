<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

/**
 * Escaping for a single JSON Pointer reference token.
 *
 * A component name is free-form, so it may contain the two characters that are structural in
 * a pointer. They have to be escaped where the name is embedded in a `$ref`, and left alone
 * where it is the key of a map — `components.schemas` holds the raw name.
 *
 * @see [RFC 6901](https://datatracker.ietf.org/doc/html/rfc6901#section-3)
 */
final class JsonPointer
{
    /**
     * `~` is escaped before `/`, so that the `~1` produced by a slash is not itself escaped.
     */
    public static function encode(string $token): string
    {
        return str_replace('/', '~1', str_replace('~', '~0', $token));
    }

    /**
     * `~1` is decoded before `~0`, so that a literal `~1` written as `~01` survives.
     */
    public static function decode(string $token): string
    {
        return str_replace('~0', '~', str_replace('~1', '/', $token));
    }

    /**
     * Builds a local `$ref` from reference tokens, escaping each.
     *
     * Every argument is one token, so a name is passed whole and never pre-joined —
     * `ref('components', 'schemas', 'Odd/Name')`, not `ref('components/schemas/Odd/Name')`.
     * Structural segments cost nothing to pass through, since neither character is legal in
     * a bucket name.
     */
    public static function ref(string ...$tokens): string
    {
        return '#/' . implode('/', array_map(self::encode(...), $tokens));
    }
}
