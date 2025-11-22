<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Attributes as OAT;

#[OAT\Schema()]
interface SecondInterface
{
    #[OAT\Property]
    public function foo(): string;
}
