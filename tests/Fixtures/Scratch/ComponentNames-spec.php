<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

// None of these carry a component key. Declared on a class, the class is the key — so each
// compiles under its short name and a `$ref` given as the class resolves to it. The classic
// pair spells the same keys out by hand, and both produce the one expected document.

#[OA\Components]
#[OA\Response(description: 'Something went wrong')]
class ErrorResponse
{
}

#[OA\Components]
#[OA\Header(description: 'Requests left in the window', schema: new OA\Schema(type: 'integer'))]
class RateLimitHeader
{
}

#[OA\Components]
#[OA\Example(summary: 'A minimal pet', value: 'Rex')]
class PetExample
{
}

#[OA\Components]
#[OA\Link(operationId: 'getPet', description: 'The pet that was found')]
class PetLink
{
}

#[OA\Info(title: 'ComponentNames', version: '1.0')]
#[OA\Operation\Get(
    path: '/pet',
    operationId: 'getPet',
    responses: [
        new OA\Response(
            response: 200,
            description: 'A pet',
            headers: [
                new OA\Header(header: 'X-Rate-Limit', ref: RateLimitHeader::class),
            ],
            content: [
                new OA\MediaType\Json(
                    type: 'string',
                    examples: [
                        new OA\Example(example: 'minimal', ref: PetExample::class),
                    ]
                ),
            ],
            links: [
                new OA\Link(link: 'pet', ref: PetLink::class),
            ]
        ),
        new OA\Response(response: 500, ref: ErrorResponse::class),
    ]
)]
class ComponentNamesEndpointSpec
{
}
