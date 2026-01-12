<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\OpenApi(
    info: new OAT\Info(
        title: 'test',
        description: 'test',
        version: '2.0.0'
    ),
    tags: [
        // definding tag 'other' globally with nice description
        new OAT\Tag('other', 'Other description'),
    ]
)]
class SurplusTag
{
    #[OAT\Get(path: '/world/', tags: ['tag world'], responses: [new OAT\Response(response: 200, description: 'success')])]
    #[OAT\Get(path: '/hello/', tags: ['tag hello'], responses: [new OAT\Response(response: 200, description: 'success')])]
    #[OAT\Get(path: '/other/', tags: ['other'], responses: [new OAT\Response(response: 200, description: 'success')])]
    // also definding tag 'other' with another description
    #[OAT\Tag('other', 'Another description')]
    public function hello(): void
    {
    }
}
