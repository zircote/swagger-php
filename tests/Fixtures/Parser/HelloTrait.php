<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Parser;

use OpenApi\Attributes as OAT;
use OpenApi\Tests\Fixtures\Parser\AsTrait as Aliased;

#[OAT\Schema(schema: 'hello')]
trait HelloTrait
{
    use OtherTrait, Aliased;

    #[OAT\Property]
    public $greet = 'Hello!';
}
