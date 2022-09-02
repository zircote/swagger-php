<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Nesting;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema
 */
class ActualModel extends SoCloseModel
{
    /**
     * @OA\Property
     *
     * @var string
     */
    public $actual;
}
