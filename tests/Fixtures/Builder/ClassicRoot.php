<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Builder;

use OpenApi\Attributes as OAT;

#[OAT\OpenApi(openapi: '3.1.0')]
#[OAT\Info(title: 'From attributes', version: '2.0.0')]
#[OAT\Server(url: 'https://api.example.com')]
#[OAT\ExternalDocumentation(url: 'https://api.example.com/docs')]
class ClassicRoot
{
    #[OAT\Get(path: '/things', operationId: 'listThings', responses: [new OAT\Response(response: 200, description: 'OK')])]
    public function list(): void
    {
    }
}
