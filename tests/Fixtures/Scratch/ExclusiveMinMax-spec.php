<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'minMaxClass')]
class MinMaxClassSpec
{
    #[OA\Property]
    #[OA\Schema(minimum: 10)]
    private int $min = 10;
    #[OA\Property]
    #[OA\Schema(minimum: 20, exclusiveMinimum: true)]
    private int $exclusiveMin = 21;
    #[OA\Property]
    #[OA\Schema(maximum: 30)]
    private int $max = 30;
    #[OA\Property]
    #[OA\Schema(maximum: 40, exclusiveMaximum: true)]
    private int $exclusiveMax = 41;

    #[OA\Property]
    #[OA\Schema(minimum: 50, exclusiveMinimum: true, maximum: 60, exclusiveMaximum: true)]
    private int $exclusiveMinMax = 51;

    #[OA\Property]
    #[OA\Schema(exclusiveMinimum: 60, exclusiveMaximum: 70)]
    private int $exclusiveMinMaxNumber = 61;
}

#[OA\Info(
    title: 'Exclusive minimum and maximum',
    version: '1.0'
)]
class ExclusiveMinMaxSpec
{
    #[OA\Operation\Get(
        path: '/api/endpoint',
        description: 'An endpoint',
        operationId: 'exclusiveMinMax',
    )]
    #[OA\Response(response: 200, description: 'OK')]
    public function exclusiveMinMaxSpec()
    {
    }
}
