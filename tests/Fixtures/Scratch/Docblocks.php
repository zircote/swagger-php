<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\OpenApi(openapi: '3.0.0')]
#[OAT\Schema]
class DocblocksSchema
{
    /**
     * @var string The name
     */
    #[OAT\Property]
    public $name;

    /**
     * @var string The name (old)
     *
     * @deprecated
     */
    #[OAT\Property]
    public $oldName;

    /**
     * @var int<5,25> The range integer
     */
    #[OAT\Property]
    public $rangeInt;

    /**
     * @var int<2,max> The minimum range integer
     */
    #[OAT\Property]
    public $minRangeInt;

    /**
     * @var int<min,10> The maximum range integer
     */
    #[OAT\Property]
    public $maxRangeInt;

    /**
     * @var positive-int The positive integer
     */
    #[OAT\Property]
    public $positiveInt;

    /**
     * @var negative-int The negative integer
     */
    #[OAT\Property]
    public $negativeInt;

    /**
     * @var non-positive-int The non-positive integer
     */
    #[OAT\Property]
    public $nonPositiveInt;

    /**
     * @var non-negative-int The non-negative integer
     */
    #[OAT\Property]
    public $nonNegativeInt;

    /**
     * @var non-zero-int The non-zero integer
     */
    #[OAT\Property]
    public $nonZeroInt;
}

#[OAT\Schema]
class DocblockSchemaChild extends DocblocksSchema
{
    /** @var int The id */
    #[OAT\Property]
    public $id;

    /**
     * Some other name.
     */
    #[OAT\Property(description: null)]
    public string $someOtherName;
}

#[OAT\Info(title: 'Docblocks', version: '1.0')]
class DocblocksEndpoint
{
    /**
     * @param string|null $filter Optional filter
     * @param int|null    $limit  Optional limit
     */
    #[OAT\Get(
        path: '/api/endpoint',
    )]
    #[OAT\Response(response: 200, description: 'successful operation')]
    public function endpoint(
        #[OAT\QueryParameter(description: null)] ?string $filter,
        #[OAT\QueryParameter] ?int $limit,
    ) {

    }
}
