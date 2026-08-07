<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Tag(name: 'sandbox', description: 'Sandbox tag')]
#[OA\Tag(name: 'unused', description: 'Not used')]
#[OA\Tag(name: 'nested', description: 'Nested tag as of 3.2.0', summary: 'Nested tag', parent: 'sandbox')]
#[OA\Tag(name: 'invalidparent', parent: 'nah')]
#[OA\Info(
    title: 'Tags',
    description: 'Tag Scratch',
    version: '1.0',
    contact: new OA\Contact(name: 'contact', email: 'contact@example.com')
)
]
#[OA\Operation\Get(
    path: '/endpoint',
    description: 'Sandbox endpoint',
    operationId: 'tagsEndpoint',
    tags: ['sandbox', 'other', 'nested', 'invalidparent'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'All good'
        ),
    ]
)]
class TagsEndpointSpec
{
}
