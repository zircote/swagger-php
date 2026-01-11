<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Attributes;

use OpenApi\Attributes as OAT;

/**
 * A Name.
 */
#[OAT\Schema]
trait NameTrait
{
    #[OAT\Property(description: 'The name.')]
    public $name;
}
