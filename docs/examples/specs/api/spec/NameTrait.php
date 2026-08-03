<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Spec;

use OpenApi\Spec as OA;

/**
 * A Name.
 */
#[OA\Schema]
trait NameTrait
{
    #[OA\Property]
    #[OA\Schema(description: 'The name.')]
    public $name;
}
