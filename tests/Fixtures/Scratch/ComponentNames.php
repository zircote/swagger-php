<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

// Classic has no class-name inference for these — a component declared without its key
// is an error there — so the keys are spelled out. They are the short names of the classes
// the spec pair declares, which is the whole point: inferring gives the same document.

#[OAT\Components(
    responses: [
        new OAT\Response(response: 'ErrorResponse', description: 'Something went wrong'),
    ],
    headers: [
        new OAT\Header(header: 'RateLimitHeader', description: 'Requests left in the window', schema: new OAT\Schema(type: 'integer')),
    ],
    links: [
        new OAT\Link(link: 'PetLink', operationId: 'getPet', description: 'The pet that was found'),
    ],
    examples: [
        new OAT\Examples(example: 'PetExample', summary: 'A minimal pet', value: 'Rex'),
    ]
)]
class ComponentNamesComponents
{
}

#[OAT\Info(title: 'ComponentNames', version: '1.0')]
#[OAT\Get(
    path: '/pet',
    operationId: 'getPet',
    responses: [
        new OAT\Response(
            response: 200,
            description: 'A pet',
            headers: [
                new OAT\Header(header: 'X-Rate-Limit', ref: '#/components/headers/RateLimitHeader'),
            ],
            content: [
                new OAT\JsonContent(
                    type: 'string',
                    examples: [
                        new OAT\Examples(example: 'minimal', ref: '#/components/examples/PetExample'),
                    ]
                ),
            ],
            links: [
                new OAT\Link(link: 'pet', ref: '#/components/links/PetLink'),
            ]
        ),
        new OAT\Response(response: 500, ref: '#/components/responses/ErrorResponse'),
    ]
)]
class ComponentNamesEndpoint
{
}
