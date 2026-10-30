<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'DiscriminatorMappingCat', properties: ['type' => OA\Type::String])]
class DiscriminatorMappingCatSpec
{
}

#[OA\Schema(component: 'DiscriminatorMappingDog', properties: ['type' => OA\Type::String])]
class DiscriminatorMappingDogSpec
{
}

#[OA\Schema(
    component: 'DiscriminatorMappingPet',
    oneOf: [
        new OA\Schema(ref: DiscriminatorMappingCatSpec::class),
        new OA\Schema(ref: DiscriminatorMappingDogSpec::class),
    ],
    discriminator: new OA\Discriminator(
        propertyName: 'type',
        mapping: ['cat' => DiscriminatorMappingCatSpec::class, 'dog' => DiscriminatorMappingDogSpec::class],
    ),
)]
class DiscriminatorMappingPetSpec
{
}

#[OA\Schema(component: 'DiscriminatorMappingShelter')]
class DiscriminatorMappingShelterSpec
{
    #[OA\Property]
    #[OA\Schema(
        type: 'array',
        items: new OA\Schema\Items(
            oneOf: [
                new OA\Schema(ref: DiscriminatorMappingCatSpec::class),
                new OA\Schema(ref: DiscriminatorMappingDogSpec::class),
            ],
            discriminator: new OA\Discriminator(
                propertyName: 'type',
                mapping: ['cat' => DiscriminatorMappingCatSpec::class, 'dog' => DiscriminatorMappingDogSpec::class],
            ),
        ),
    )]
    public array $pets;
}

#[OA\Info(
    title: 'Discriminator Mapping Scratch',
    version: '1.0'
)]
#[OA\Operation\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'getDiscriminatorMapping',
)]
#[OA\Response(response: 200, description: 'OK')]
class DiscriminatorMappingEndpointSpec
{
}
