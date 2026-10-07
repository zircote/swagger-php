<?php

namespace OpenApi\Rector\Tests\Fixture\UnnestOpenApi;

use OpenApi\Attributes as OA;

#[
    OA\OpenApi(
        openapi: '3.0.0',
        info: new OA\Info(title: 'Api', version: '1'),
        servers: [new OA\Server(url: 'https://example.com')],
        security: [['ApiKey' => []]],
        tags: [new OA\Tag(name: 'public'), new OA\Tag(name: 'admin')],
        externalDocs: new OA\ExternalDocumentation(url: 'https://example.com/docs'),
        x: ['tagGroups' => []]
    ),
    OA\SecurityScheme(securityScheme: 'ApiKey', type: 'apiKey', name: 'X-API-Key', in: 'header')
]
class Api
{
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\UnnestOpenApi;

use OpenApi\Attributes as OA;

#[
    OA\OpenApi(version: '3.0.0', security: [['ApiKey' => []]], x: ['tagGroups' => []]),
    OA\Info(title: 'Api', version: '1'),
    OA\Server(url: 'https://example.com'),
    OA\Tag(name: 'public'),
    OA\Tag(name: 'admin'),
    OA\ExternalDocumentation(url: 'https://example.com/docs'),
    OA\SecurityScheme(securityScheme: 'ApiKey', type: 'apiKey', name: 'X-API-Key', in: 'header')
]
class Api
{
}

?>
