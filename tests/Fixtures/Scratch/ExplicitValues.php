<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'ExplicitValuesPet')]
class ExplicitValuesPet
{
    #[OAT\Property]
    public string $name;
}

#[OAT\Schema(schema: 'ExplicitValues')]
class ExplicitValues
{
    // an explicit bound is kept, the other one comes from the docblock range
    /**
     * @var int<1, 10>
     */
    #[OAT\Property(minimum: 5)]
    public int $count;

    // an explicit enum is kept instead of a reference to the component
    #[OAT\Property(enum: ['cat', 'dog'])]
    public ExplicitValuesPet $kind;

    // an explicit class type still becomes a reference
    #[OAT\Property(type: ExplicitValuesPet::class)]
    public mixed $pet;
}

#[OAT\Info(title: 'ExplicitValues', version: '1.0')]
#[OAT\Get(
    path: '/explicit-values',
    operationId: 'ExplicitValuesEndpoint',
    responses: [
        new OAT\Response(
            response: 200,
            description: 'All good',
            content: new OAT\JsonContent(ref: ExplicitValues::class)
        ),
    ]
)]
class ExplicitValuesEndpoint
{
}
