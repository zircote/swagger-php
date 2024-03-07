<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;

/** @OA\Schema  */
class DocblockSchema
{
    /**
     * @OA\Property
     * @var string The name
     */
    public $name;

    /**
     * @OA\Property
     * @var string The name (old)
     *
     * @deprecated
     */
    public $oldName;

    /**
     * @OA\Property
     * @var int<5,25> The range integer
     */
    public $rangeInt;

    /**
     * @OA\Property
     * @var int<2,max> The minimum range integer
     */
    public $minRangeInt;

    /**
     * @OA\Property
     * @var int<min,10> The maximum range integer
     */
    public $maxRangeInt;

    /**
     * @OA\Property
     * @var positive-int The positive integer
     */
    public $positiveInt;

    /**
     * @OA\Property
     * @var negative-int The negative integer
     */
    public $negativeInt;

    /**
     * @OA\Property
     * @var non-positive-int The non-positive integer
     */
    public $nonPositiveInt;

    /**
     * @OA\Property
     * @var non-negative-int The non-negative integer
     */
    public $nonNegativeInt;

    /**
     * @OA\Property
     * @var non-zero-int The non-zero integer
     */
    public $nonZeroInt;
}

#[OAT\Schema]
class DocblockSchemaChild extends DocblockSchema
{
    /** @var int The id */
    #[OAT\Property]
    public $id;
}

/**
 * @OA\Info(title="API", version="1.0")
 * @OA\Get(
 *     path="/api/endpoint",
 *     @OA\Response(
 *         response=200,
 *         description="successful operation"
 *     )
 * )
 */
class DockblockEndpoint
{
}
