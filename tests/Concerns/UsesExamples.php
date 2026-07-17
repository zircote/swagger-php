<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Concerns;

use Composer\Autoload\ClassLoader;
use OpenApi\Builder\Mode;

trait UsesExamples
{
    public static function examplePath(string $name): string
    {
        return sprintf('%s/docs/examples/specs/%s', dirname(__DIR__, 2), $name);
    }

    public static function getSpecFilename(string $name, string $implementation = 'annotations', string $version = '3.0.0', Mode $mode = Mode::CLASSIC): string
    {
        $specs = [
            "{$name}-{$implementation}-{$mode->value}-{$version}.yaml",
            "{$name}-{$implementation}-{$version}.yaml",
            "{$name}-{$mode->value}-{$version}.yaml",
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
        $classloader->addPsr4($implementationNamerspaceBase, $path);

        $sharedFiles = glob("{$basePath}/*.php");
        if ($sharedFiles) {
            $classMap = [];
            foreach ($sharedFiles as $file) {
                $className = $namerspaceBase . pathinfo($file, PATHINFO_FILENAME);
                $classMap[$className] = $file;
            }
            $classloader->addClassMap($classMap);
        }

        $classloader->register();
    }
}
