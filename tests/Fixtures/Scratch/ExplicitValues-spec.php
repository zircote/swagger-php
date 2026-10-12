<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'ExplicitValuesPet')]
class ExplicitValuesPetSpec
{
    #[OA\Property]
    public string $name;
}

#[OA\Schema(component: 'ExplicitValues')]
class ExplicitValuesSpec
{
    // an explicit bound is kept, the other one comes from the docblock range
    /**
     * @var int<1, 10>
     */
    #[OA\Property]
    #[OA\Schema(minimum: 5)]
    public int $count;

    // an explicit enum is kept instead of a reference to the component
    #[OA\Property]
    #[OA\Schema(enum: ['cat', 'dog'])]
    public ExplicitValuesPetSpec $kind;

    // an explicit class type still becomes a reference
    #[OA\Property]
    #[OA\Schema(ref: ExplicitValuesPetSpec::class)]
    public mixed $pet;
}

#[OA\Info(title: 'ExplicitValues', version: '1.0')]
#[OA\Operation\Get(
    path: '/explicit-values',
    operationId: 'ExplicitValuesEndpoint',
)]
#[OA\Response(
    response: 200,
    description: 'All good',
    content: new OA\MediaType\Json(ref: ExplicitValuesSpec::class)
)]
class ExplicitValuesEndpointSpec
{
}
