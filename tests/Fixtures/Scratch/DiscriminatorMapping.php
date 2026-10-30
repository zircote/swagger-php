<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

/*
 * A discriminator mapping given as class names, once on a component schema and once on a schema
 * nested in a property. Both resolve to component pointers.
 */

#[OAT\Schema(properties: [new OAT\Property(property: 'type', type: 'string')])]
class DiscriminatorMappingCat
{
}

#[OAT\Schema(properties: [new OAT\Property(property: 'type', type: 'string')])]
class DiscriminatorMappingDog
{
}

#[OAT\Schema(
    oneOf: [
        new OAT\Schema(ref: DiscriminatorMappingCat::class),
        new OAT\Schema(ref: DiscriminatorMappingDog::class),
    ],
    discriminator: new OAT\Discriminator(
        propertyName: 'type',
        mapping: ['cat' => DiscriminatorMappingCat::class, 'dog' => DiscriminatorMappingDog::class],
    ),
)]
class DiscriminatorMappingPet
{
}

#[OAT\Schema(
    required: ['pets'],
    properties: [
        new OAT\Property(
            property: 'pets',
            type: 'array',
            items: new OAT\Items(
                oneOf: [
                    new OAT\Schema(ref: DiscriminatorMappingCat::class),
                    new OAT\Schema(ref: DiscriminatorMappingDog::class),
                ],
                discriminator: new OAT\Discriminator(
                    propertyName: 'type',
                    mapping: ['cat' => DiscriminatorMappingCat::class, 'dog' => DiscriminatorMappingDog::class],
                ),
            ),
        ),
    ],
)]
class DiscriminatorMappingShelter
{
}

#[OAT\Info(
    title: 'Discriminator Mapping Scratch',
    version: '1.0'
)]
#[OAT\Get(
    path: '/api/endpoint',
    description: 'An endpoint',
    operationId: 'getDiscriminatorMapping',
    responses: [new OAT\Response(response: 200, description: 'OK')]
)]
class DiscriminatorMappingEndpoint
{
}
