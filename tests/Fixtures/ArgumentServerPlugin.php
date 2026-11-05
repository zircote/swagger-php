<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

/**
 * A plugin that needs a constructor argument, so it cannot be named by its class.
 */
class ArgumentServerPlugin extends ServerPlugin
{
    public function __construct(string $url)
    {
    }
}
