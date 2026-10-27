<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\KeyOrder;

use OpenApi\Attributes as OAT;

#[OAT\OpenApi(
    openapi: '3.1.0',
    info: new OAT\Info(version: '1.0.0', title: 'Key order'),
    servers: [new OAT\Server(url: 'https://example.com')],
    security: [['apiKey' => []]],
    tags: [new OAT\Tag(name: 'pets')],
    externalDocs: new OAT\ExternalDocumentation(url: 'https://example.com/docs'),
    webhooks: [
        new OAT\Webhook(
            webhook: 'newPet',
            post: new OAT\Post(
                operationId: 'newPet',
                responses: [new OAT\Response(response: 200, description: 'ok')],
            ),
        ),
    ],
)]
#[OAT\SecurityScheme(securityScheme: 'apiKey', type: 'apiKey', name: 'X-Key', in: 'header')]
class EveryTopLevelKey
{
    #[OAT\Get(path: '/pets', operationId: 'listPets', tags: ['pets'], responses: [new OAT\Response(response: 200, description: 'ok')])]
    public function list(): void
    {
    }
}
