<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use Composer\Autoload\ClassLoader;

/**
 * Scans for classes/interfaces/traits.
 *
 * Relies on a `composer --optimized` run in order to utilize
 * the generated class map.
 */
class ComposerAutoloaderScanner
{
    /**
     * Collect all classes/interfaces/traits known by composer.
     *
     * @param array<string> $namespaces
     *
     * @return array<string>
     */
    public function scan(array $namespaces): array
    {
        $units = [];
        if ($autoloader = $this->getComposerAutoloader()) {
            foreach (array_keys($autoloader->getClassMap()) as $unit) {
                foreach ($namespaces as $namespace) {
                    if (0 === strpos($unit, $namespace)) {
                        $units[] = $unit;
                        break;
                    }
                }
            }
        }

        return $units;
    }

    public static function getComposerAutoloader(): ?ClassLoader
    {
        foreach (spl_autoload_functions() as $fkt) {
            if (is_array($fkt) && $fkt[0] instanceof ClassLoader) {
                return $fkt[0];
            }
        }

        return null;
    }
}
