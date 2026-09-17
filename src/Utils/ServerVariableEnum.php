<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

use Psr\Log\LoggerInterface;

/**
 * Normalises a server variable's `enum` to the strings the OpenAPI specification allows.
 *
 * The Server Variable Object's `enum` is `[string]` in 3.0, 3.1 and 3.2 alike, and a server
 * variable is substituted into a URL template, so every value is textual in the end. Classic's
 * annotation accepts more than that, which is why this exists — and why it lives here rather
 * than in one pipeline: classic resolves enums in a processor, hybrid and spec in an augmenter,
 * and the bridge has to hand the spec model a `list<string>` before either runs.
 */
final class ServerVariableEnum
{
    /**
     * @param iterable<mixed> $enum
     *
     * @return list<string>
     */
    public static function asStrings(iterable $enum, ?LoggerInterface $logger = null, ?string $variable = null): array
    {
        $values = [];
        // Built here rather than taken from the caller so the three pipelines word it the same;
        // classic's identity() would read differently and split the log expectations.
        $identity = 'ServerVariable(' . ($variable ?? '?') . ')';

        foreach ($enum as $value) {
            if (is_bool($value)) {
                // Unlike a number, a bool has no textual form to substitute — PHP would give
                // '1' and '', and neither is a URL segment anyone meant to write.
                $logger?->warning($identity . ': enum values must be strings, dropping ' . var_export($value, true));

                continue;
            }

            if (is_int($value) || is_float($value)) {
                $values[] = (string) $value;

                continue;
            }

            if (is_string($value)) {
                $values[] = $value;

                continue;
            }

            $logger?->warning($identity . ': enum values must be strings, dropping ' . get_debug_type($value));
        }

        return $values;
    }
}
