<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

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
    tags: ['sandbox'],
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
