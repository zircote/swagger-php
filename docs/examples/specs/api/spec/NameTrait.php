<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Spec;

use OpenApi\Spec as OA;

/**
 * A Name.
 */
#[OA\Schema(schema: 'NameTrait')]
trait NameTrait
{
    #[OA\Property(property: 'name')]
    #[OA\Schema(description: 'The name.')]
    public $name;
}
