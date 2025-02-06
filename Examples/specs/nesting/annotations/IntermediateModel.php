<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Nesting\Annotations;

use OpenApi\Annotations as OA;

/**
 * No schema!
 */
class IntermediateModel extends BaseModel
{
    /**
     * @OA\Property
     *
     * @var string
     */
    public $intermediate;
}
