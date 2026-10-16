<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

/**
 * A foreign attribute holding a closure, as Symfony's `#[IsGranted]` can since PHP 8.5.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class ClosureAttribute
{
    public \Closure $check;

    public function __construct()
    {
        $this->check = static fn (): bool => true;
    }
}

#[OAT\Info(title: 'Closure in a foreign attribute', version: '1.0')]
class ClosureInForeignAttribute
{
    #[OAT\Get(path: '/things', responses: [new OAT\Response(response: 200, description: 'OK')])]
    #[ClosureAttribute]
    public function things(): void
    {
    }
}
