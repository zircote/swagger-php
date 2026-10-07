<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'YoYo')]
class ClassRefSpec
{
}

#[OA\Info(title: 'ClassRef', version: '1.0')]
#[OA\Operation\Get(
    path: '/endpoint',
    operationId: 'ClassRefEndpoint',
)]
#[OA\Response(
    response: 200,
    description: 'All good',
    content: new OA\MediaType\Json(ref: ClassRefSpec::class)
)]
class ClassRefEndpointSpec
{
}

// a property typed with a class infers a reference only when the class is a component
class ClassRefPlainSpec
{
}

#[OA\Schema(component: 'ClassRefHolder')]
class ClassRefHolderSpec
{
    #[OA\Property]
    public ClassRefSpec $component;

    #[OA\Property]
    public ClassRefPlainSpec $plain;

    #[OA\Property]
    public ?ClassRefPlainSpec $nullablePlain;
}

#[OA\Operation\Get(
    path: '/holder',
    operationId: 'ClassRefHolderEndpoint',
)]
#[OA\Response(
    response: 200,
    description: 'Holder',
    content: new OA\MediaType\Json(ref: ClassRefHolderSpec::class)
)]
class ClassRefHolderEndpointSpec
{
}
