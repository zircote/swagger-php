<?php declare(strict_types=1);

namespace OpenApi\Examples\Nesting;

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
