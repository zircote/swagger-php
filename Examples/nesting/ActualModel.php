<?php declare(strict_types=1);

namespace OpenApi\Examples\Nesting;

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
