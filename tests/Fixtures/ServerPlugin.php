<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Builder\Hooks;
use OpenApi\Contracts\PluginInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;

/**
 * Contributes one server, at `https://plugin.example` or at the `url` setting.
 */
class ServerPlugin implements PluginInterface
{
    public function configure(Hooks $hooks, array $config): void
    {
        $url = $config['url'] ?? 'https://plugin.example';
        assert(is_string($url));

        $hooks->withSpecification(fn (Specification $specification): Specification => $specification->add(new OA\Server(url: $url)));
    }
}
