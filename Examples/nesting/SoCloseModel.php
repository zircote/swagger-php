<?php declare(strict_types=1);

namespace OpenApi\Examples\Nesting;

/**
 * No schema!
 */
class SoCloseModel extends AlmostModel
{
    /**
     * @OA\Property
     *
     * @var string
     */
    public $soClose;
}
