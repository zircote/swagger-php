<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

/**
 * Normalises one security requirement to the scheme name => scopes map the OpenAPI specification defines.
 *
 * A requirement may also be written as a list of scheme names, `['bearerAuth']`, meaning those
 * schemes with no scopes. Classic serialises its annotations, while hybrid and spec compile
 * `Security\Requirement`, so this lives here for all three to share.
 */
final class SecurityRequirement
{
    /**
     * @param array<mixed> $requirement
     *
     * @return array<mixed>
     */
    public static function normalise(array $requirement): array
    {
        $normalised = [];
        foreach ($requirement as $key => $value) {
            if (is_int($key) && is_string($value)) {
                $normalised[$value] = [];
            } else {
                $normalised[$key] = $value;
            }
        }

        return $normalised;
    }
}
