<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

/**
 * Safely reflects a class/interface/trait/enum name.
 *
 * `class_exists()` and friends normally just answer "does this name resolve", but with
 * autoloading enabled a name that resolves to a file which will not link — a parent class
 * or interface it depends on is missing — throws instead of returning `false`. This wraps
 * that so a single unloadable type costs the caller one name, not a fatal error.
 */
class ClassReflector
{
    /**
     * A reflector for the name, or the reason there is none.
     *
     * `$fqdn` is deliberately plain `string`, not `class-string`: the whole point of this
     * method is to accept a name a caller does not yet know resolves to anything.
     *
     * The reason is `null` when nothing by that name exists, and the throwable's message when the
     * name exists but would not load.
     *
     * @return array{0: \ReflectionClass<object>|null, 1: string|null} the reflector, or the reason
     */
    public static function tryReflect(string $fqdn): array
    {
        try {
            if (!class_exists($fqdn) && !interface_exists($fqdn) && !trait_exists($fqdn) && !enum_exists($fqdn)) {
                return [null, null];
            }

            return [new \ReflectionClass($fqdn), null];
        } catch (\Throwable $throwable) {
            return [null, $throwable->getMessage()];
        }
    }
}
