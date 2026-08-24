<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Concerns;

use OpenApi\Contracts\AttributeInterface;

trait CollectsSpecClasses
{
    /**
     * @return list<class-string<AttributeInterface>>
     */
    private static function allSpecClasses(): array
    {
        $classes = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(dirname(__DIR__, 2) . '/src/Spec', \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $class = 'OpenApi\\Spec\\' . str_replace(
                ['/', '.php'],
                ['\\', ''],
                substr($file->getPathname(), strlen(dirname(__DIR__, 2) . '/src/Spec/'))
            );

            if (!class_exists($class) || !(new \ReflectionClass($class))->isInstantiable()) {
                continue;
            }

            if (!is_subclass_of($class, AttributeInterface::class)) {
                continue;
            }

            $classes[] = $class;
        }

        sort($classes);

        return $classes;
    }
}
