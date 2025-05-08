<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Annotations;

use OpenApi\Annotations as OA;

/**
 * A Name.
 *
 * @OA\Schema
 */
trait NameTrait
{
    /**
     * The name.
     *
     * @OA\Property
     */
    public $name;
}
