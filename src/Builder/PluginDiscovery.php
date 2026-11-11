<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Builder;

use Composer\Autoload\ClassLoader;
use OpenApi\Builder;
use Psr\Log\LoggerInterface;

/**
 * Finds the plugins installed packages declare in their `composer.json`.
 *
 * A package declares its plugins as a list of class names under `extra.swagger-php.plugins`. They
 * are read from Composer's `installed.json`, by default from the installation swagger-php itself
 * was loaded from.
 *
 * The order is fixed, so a build does not change when Composer rewrites the file: a package's
 * plugins come after those of the packages it requires, and packages otherwise go by name.
 */
class PluginDiscovery
{
    public function __construct(protected LoggerInterface $logger)
    {
    }

    /**
     * @param string|null $vendorDir the Composer vendor directory to read; the one swagger-php was loaded from when not given
     *
     * @return array<string, string> each plugin class, mapped to the package declaring it
     */
    public function plugins(?string $vendorDir = null): array
    {
        $vendorDir ??= $this->defaultVendorDir();
        if ($vendorDir === null) {
            $this->logger->warning('No Composer installation found to discover plugins in');

            return [];
        }

        $packages = $this->packages($vendorDir);
        $plugins = [];
        $seen = [];
        foreach ($this->ordered($packages) as $name) {
            foreach ($this->declared($packages[$name]) as $class) {
                if (!isset($seen[self::classKey($class)])) {
                    $seen[self::classKey($class)] = true;
                    $plugins[ltrim($class, '\\')] = $name;
                }
            }
        }

        return $plugins;
    }

    /**
     * A class name as PHP compares it: without a leading backslash, and ignoring case.
     */
    public static function classKey(string $class): string
    {
        return strtolower(ltrim($class, '\\'));
    }

    /**
     * The vendor directory of the Composer class loader that loads swagger-php, read when no other is given.
     */
    public function defaultVendorDir(): ?string
    {
        if (!class_exists(ClassLoader::class)) {
            return null;
        }

        foreach (ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
            if ($loader->findFile(Builder::class) !== false) {
                return $vendorDir;
            }
        }

        return null;
    }

    /**
     * The installed packages by name.
     *
     * @return array<string, array<string, mixed>>
     */
    protected function packages(string $vendorDir): array
    {
        $filename = $vendorDir . '/composer/installed.json';
        $contents = is_file($filename) ? file_get_contents($filename) : false;
        if ($contents === false) {
            $this->logger->warning(sprintf('Cannot read "%s" to discover plugins', $filename));

            return [];
        }

        try {
            $installed = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $jsonException) {
            $this->logger->warning(sprintf('Cannot read "%s" to discover plugins: %s', $filename, $jsonException->getMessage()));

            return [];
        }

        // Composer 2 wraps the list, Composer 1 wrote it bare
        $list = is_array($installed) ? ($installed['packages'] ?? $installed) : [];

        $packages = [];
        foreach (is_array($list) ? $list : [] as $package) {
            if (is_array($package) && is_string($package['name'] ?? null)) {
                $packages[$package['name']] = $package;
            }
        }

        return $packages;
    }

    /**
     * Package names, each after the packages it requires, and otherwise by name.
     *
     * @param array<string, array<string, mixed>> $packages
     *
     * @return list<string>
     */
    protected function ordered(array $packages): array
    {
        ksort($packages);

        $ordered = [];
        $visited = [];
        foreach (array_keys($packages) as $name) {
            $this->visit($name, $packages, $visited, $ordered);
        }

        return $ordered;
    }

    /**
     * Adds a package to the order, after the packages it requires.
     *
     * @param array<string, array<string, mixed>> $packages
     * @param array<string, true>                 $visited
     * @param list<string>                        $ordered
     */
    protected function visit(string $name, array $packages, array &$visited, array &$ordered): void
    {
        if (isset($visited[$name])) {
            return;
        }
        $visited[$name] = true;

        $requires = $packages[$name]['require'] ?? [];
        $dependencies = is_array($requires) ? array_keys($requires) : [];
        sort($dependencies);
        foreach ($dependencies as $dependency) {
            if (isset($packages[$dependency])) {
                $this->visit($dependency, $packages, $visited, $ordered);
            }
        }

        $ordered[] = $name;
    }

    /**
     * The plugin classes a package declares.
     *
     * @param array<string, mixed> $package
     *
     * @return list<string>
     */
    protected function declared(array $package): array
    {
        $extra = $package['extra'] ?? null;
        if (!is_array($extra) || !isset($extra['swagger-php']['plugins'])) {
            return [];
        }

        $plugins = $extra['swagger-php']['plugins'];
        if (!is_array($plugins) || !array_is_list($plugins) || array_filter($plugins, is_string(...)) !== $plugins) {
            $this->logger->warning(sprintf('Package "%s" declares swagger-php plugins that are not a list of class names, skipped', $package['name']));

            return [];
        }

        return $plugins;
    }
}
