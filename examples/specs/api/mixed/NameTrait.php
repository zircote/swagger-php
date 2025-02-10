<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Mixed;

use OpenApi\Attributes as OAT;

/**
 * A Name.
 */
#[OAT\Schema()]
trait NameTrait
{
    /**
     * The name.
     */
    #[OAT\Property()]
    public $name;
}
