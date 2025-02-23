<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Nesting\Annotations;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema
 */
class AlmostModel extends IntermediateModel
{
    /**
     * @OA\Property
     *
     * @var string
     */
    public $almost;
}
