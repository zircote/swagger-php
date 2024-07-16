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
)]
class SurplusTag
{
    #[OAT\Get(path: '/world/', tags: ['tag world'], responses: [new OAT\Response(response: '200', description: 'success')])]
    #[OAT\Get(path: '/hello/', tags: ['tag hello'], responses: [new OAT\Response(response: '200', description: 'success')])]
    public function hello(): void
    {
    }
}
