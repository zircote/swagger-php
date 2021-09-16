<?php declare(strict_types=1);

namespace OpenApi\Examples\Nesting;

/**
 * @OA\Schema()
 */
class AlmostModel extends IntermediateModel
{
    /**
     * @OA\Property()
     *
     * @var string
     */
    public $almost;

}