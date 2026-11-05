<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Contracts;

use OpenApi\Builder\Hooks;

/**
 * Configures a builder on behalf of a library, so an integration only names the library's plugin.
 *
 * A plugin configures the builder through its hooks, and plugins build on one another by adding to
 * what the hooks hold. It cannot set the builder's services, such as the component namer: those
 * stay with whoever runs the build.
 *
 * A plugin named by its class is created without arguments; its settings come as `$config`. It
 * throws for settings it cannot use; to report what it works around, it implements
 * `LoggerAwareInterface`.
 */
interface PluginInterface
{
    /**
     * @param array<string, mixed> $config the plugin's settings, empty when none are given
     */
    public function configure(Hooks $hooks, array $config): void;
}
