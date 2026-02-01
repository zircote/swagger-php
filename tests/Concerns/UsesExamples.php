<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Concerns;

use Composer\Autoload\ClassLoader;
use OpenApi\Attributes\OpenApi;

trait UsesExamples
{
    public static function examplePath(string $name): string
    {
        return sprintf('%s/docs/examples/specs/%s', dirname(__DIR__, 2), $name);
    }

    public static function getSpecFilename(string $name, string $implementation = 'annotations', string $version = OpenApi::VERSION_3_0_0): string
    {
        $specs = [
            "{$name}-{$version}.yaml",
            "{$name}-{$implementation}-{$version}.yaml",
        ];

        $basePath = static::examplePath($name);
        foreach ($specs as $spec) {
            $specFilename = "{$basePath}/{$spec}";
            if (file_exists($specFilename)) {
                break;
            }
        }

        return $specFilename;
    }

    public function registerExampleClassloader(string $name, string $implementation = 'annotations'): void
    {
        $packageName = str_replace(' ', '', ucwords(str_replace(['-', '.'], ' ', $name)));
        $packageName = str_replace(' ', '\\', ucwords(str_replace('/', ' ', $packageName)));

        $basePath = static::examplePath($name);
        $path = "{$basePath}/{$implementation}";
        $namerspaceBase = sprintf('OpenApi\\Examples\\Specs\\%s\\', $packageName);
        $implementationNamerspaceBase = sprintf("{$namerspaceBase}%s\\", ucfirst($implementation));

        $classloader = new ClassLoader();
        $classloader->addPsr4($namerspaceBase, $basePath);
        $classloader->addPsr4($implementationNamerspaceBase, $path);
        $classloader->register();
    }
}
