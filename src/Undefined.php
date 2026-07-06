<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

final class Undefined
{
    public const UNDEFINED = '@OA\\UNDEFINED🙈';

    public static function isDefault(mixed ...$value): bool
    {
        foreach ($value as $v) {
            if ($v !== self::UNDEFINED) {
                return false;
            }
        }

        return true;
    }
}
