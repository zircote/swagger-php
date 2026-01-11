<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Parser;

use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'other')]
trait OtherTrait
{
    #[OAT\Property]
    public $so = 'what?';
}
