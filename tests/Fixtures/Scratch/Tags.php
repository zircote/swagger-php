<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Tag(name: 'sandbox', description: 'Sandbox tag')]
#[OAT\Tag(name: 'unused', description: 'Not used')]
#[OAT\Tag(name: 'nested', description: 'Nested tag as of 3.2.0', summary: 'Nested tag', parent: 'sandbox')]
#[OAT\Tag(name: 'invalidparent', parent: 'nah')]
#[OAT\Info(
    title: 'Tags',
    description: 'Tag Scratch',
    version: '1.0',
    contact: new OAT\Contact(name: 'contact', email: 'contact@example.com')
)
]
#[OAT\Get(
    path: '/endpoint',
    description: 'Sandbox endpoint',
    tags: ['sandbox', 'other', 'nested', 'invalidparent'],
    responses: [
        new OAT\Response(
            response: 200,
            description: 'All good'
        ),
    ]
)]
class TagsEndpoint
{
}
