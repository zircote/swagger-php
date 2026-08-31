<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Utils;

use OpenApi\Utils\Config;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    /**
     * A #[Config] parameter with no matching setter is silently invisible to
     * Pipeline::getConfig() — this guards against that going unnoticed.
     */
    public function testEveryAnnotatedAugmenterParameterHasAMatchingSetter(): void
    {
        $failures = [];

        foreach ($this->allAugmenterClasses() as $class) {
            $rc = new \ReflectionClass($class);

            foreach (array_keys(Config::forConstructor($rc)) as $name) {
                $setter = 'set' . ucfirst($name);
                if (!$rc->hasMethod($setter)) {
                    $failures[] = "{$class}::\${$name} carries #[Config] but has no {$setter}()";
                }
            }
        }

        $this->assertEmpty($failures, implode("\n", $failures));
    }

    /**
     * @return list<class-string>
     */
    private function allAugmenterClasses(): array
    {
        $dir = dirname(__DIR__, 2) . '/src/Augmenter';
        $classes = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $class = 'OpenApi\\Augmenter\\' . str_replace(
                ['/', '.php'],
                ['\\', ''],
                substr($file->getPathname(), strlen($dir) + 1)
            );

            if (!class_exists($class) || !(new \ReflectionClass($class))->isInstantiable()) {
                continue;
            }

            $classes[] = $class;
        }

        sort($classes);

        return $classes;
    }
}
