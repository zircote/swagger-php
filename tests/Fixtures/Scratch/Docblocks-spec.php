<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\OpenApi(version: '3.0.0')]
#[OA\Schema(schema: 'DocblocksSchema')]
class DocblocksSchemaSpec
{
    /**
     * @var string The name
     */
    #[OA\Property]
    public $name;

    /**
     * @var string The name (old)
     *
     * @deprecated
     */
    #[OA\Property]
    public $oldName;

    /**
     * @var int<5,25> The range integer
     */
    #[OA\Property]
    public $rangeInt;

    /**
     * @var int<2,max> The minimum range integer
     */
    #[OA\Property]
    public $minRangeInt;

    /**
     * @var int<min,10> The maximum range integer
     */
    #[OA\Property]
    public $maxRangeInt;

    /**
     * @var positive-int The positive integer
     */
    #[OA\Property]
    public $positiveInt;

    /**
     * @var negative-int The negative integer
     */
    #[OA\Property]
    public $negativeInt;

    /**
     * @var non-positive-int The non-positive integer
     */
    #[OA\Property]
    public $nonPositiveInt;

    /**
     * @var non-negative-int The non-negative integer
     */
    #[OA\Property]
    public $nonNegativeInt;

    /**
     * @var non-zero-int The non-zero integer
     */
    #[OA\Property]
    public $nonZeroInt;
}

#[OA\Schema(schema: 'DocblockSchemaChild')]
class DocblockSchemaChildSpec extends DocblocksSchemaSpec
{
    /** @var int The id */
    #[OA\Property]
    public $id;

    /**
     * Some other name.
     */
    #[OA\Property]
    #[OA\Schema(description: null)]
    public string $someOtherName;
}

#[OA\Info(title: 'Docblocks', version: '1.0')]
class DocblocksEndpointSpec
{
    /**
     * @param string|null $filter Optional filter
     * @param int|null    $limit  Optional limit
     */
    #[OA\Operation\Get(
        path: '/api/endpoint',
        operationId: 'DocblocksEndpoint',
    )]
    #[OA\Response(response: 200, description: 'successful operation')]
    public function endpoint(
        /** @var string|null $filter An optional filter */
        #[OA\Parameter\Query(description: null)] ?string $filter,
        /** @var string|null $limit An optional limit */
        #[OA\Parameter\Query] ?int $limit,
    ) {

    }
}
