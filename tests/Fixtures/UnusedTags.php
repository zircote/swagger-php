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
        // making a tag that has no operations referencing it, but we wish to keep it
        new OAT\Tag('fancy', 'Fancy description'),
        // making a tag that it not used, but we do not wish to keep
        new OAT\Tag('notused', 'remove this one'),
    ]
)]
class UnusedTags
{
    #[OAT\Get(path: '/other/', tags: ['other'], responses: [new OAT\Response(response: 200, description: 'success')])]
    public function hello(): void
    {
    }
}
