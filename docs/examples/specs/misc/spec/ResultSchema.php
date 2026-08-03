<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Misc\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'Result', title: 'Sample schema for using references')]
class ResultSchema
{
    #[OA\Property]
    #[OA\Schema(type: 'string')]
    public $status;

    #[OA\Property]
    #[OA\Schema(type: 'string')]
    public $error;
}
