<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Nesting\Spec;

use OpenApi\Spec as OA;

/**
 * No schema!
 */
class SoCloseModel extends AlmostModel
{
    #[OA\Property]
    public string $soClose;
}
