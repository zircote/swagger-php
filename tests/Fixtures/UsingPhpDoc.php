<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'Fixture for AugmentOperationTest', version: 'test')]
class UsingPhpDoc
{
    /**
     * Example summary.
     *
     * Example description...
     * More description...
     */
    #[OAT\Get(path: '/api/test1')]
    #[OAT\Response(response: 200, description: 'a response')]
    public function methodWithDescription()
    {
    }

    /**
     * Example summary.
     */
    #[OAT\Get(path: '/api/test2')]
    #[OAT\Response(response: 200, description: 'a response')]
    public function methodWithSummary()
    {
    }
}
