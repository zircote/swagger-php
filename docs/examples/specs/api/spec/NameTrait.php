<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Spec\Fixtures\Api;

use OpenApi\Spec as OA;

/**
 * A Name.
 */
#[OA\Schema(schema: 'NameTrait')]
trait NameTrait
{
    #[OA\Property(property: 'name', schema: new OA\Schema(description: 'The name.'))]
    public $name;
}
