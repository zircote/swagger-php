<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'MultiTypeProperty')]
class MultiTypePropertySpec
{
    #[OA\Property]
    #[OA\Schema(example: true)]
    public int|bool|null $value;

    /**
     * @var string|list<string>
     */
    #[OA\Property]
    #[OA\Schema(example: 'My value')]
    public string|array $mixedUnion;

    /**
     * @param string|list<string> $otherValue
     */
    public function __construct(
        #[OA\Property]
        #[OA\Schema(example: 'My value')]
        public string|array $otherValue,
    ) {
    }
}

#[OA\Info(
    title: 'Multi Typed Property Scratch',
    version: '1.0'
)]
#[OA\Operation\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'multiTypePropertyEndpoint',
)]
#[OA\Response(response: 200, description: 'OK')]
class MultiTypePropertyEndpointSpec
{
}
