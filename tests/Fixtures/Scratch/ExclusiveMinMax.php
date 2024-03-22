<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'minMaxClass')]
class MinMaxClass
{
    #[OAT\Property(minimum: 10)]
    private int $min = 10;
    #[OAT\Property(minimum: 20, exclusiveMinimum: true)]
    private int $exclusiveMin = 21;
    #[OAT\Property(maximum: 30)]
    private int $max = 30;
    #[OAT\Property(maximum: 40, exclusiveMaximum: true)]
    private int $exclusiveMax = 41;

    #[OAT\Property(minimum: 50, exclusiveMinimum: true, maximum: 60, exclusiveMaximum: true)]
    private int $exclusiveMinMax = 51;

    #[OAT\Property(exclusiveMinimum: 60, exclusiveMaximum: 70)]
    private int $exclusiveMinMaxNumber = 61;
}

#[OAT\Info(
    title: 'Exclusive minimum and maximum',
    version: '1.0'
)]
class ExclusiveMinMax
{
    #[OAT\Get(
        path: '/api/endpoint',
        description: 'An endpoint',
        responses: [new OAT\Response(response: 200, description: 'OK')]
    )]
    public function exclusiveMinMax()
    {
    }
}
