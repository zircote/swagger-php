<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PluginDiscovery;

use OpenApi\Builder\Hooks;
use OpenApi\Contracts\PluginInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;

/**
 * Contributes one server named after the plugin, so a test can read the order plugins ran in.
 */
abstract class NamedServerPlugin implements PluginInterface
{
    public function configure(Hooks $hooks, array $config): void
    {
        $name = strtolower(str_replace('Plugin', '', (new \ReflectionClass($this))->getShortName()));
        $url = $config['url'] ?? "https://{$name}.example";
        assert(is_string($url));

        $hooks->withSpecification(fn (Specification $specification): Specification => $specification->add(new OA\Server(url: $url)));
    }
}
