<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'Security shapes', version: '1.0')]
#[OAT\SecurityScheme(securityScheme: 'bearer', type: 'http', scheme: 'bearer')]
#[OAT\SecurityScheme(securityScheme: 'api_key', type: 'apiKey', name: 'api_key', in: 'header')]
class SecurityShapes
{
    // classic reads a bare scheme name as that scheme with no scopes
    #[OAT\Get(path: '/string', operationId: 'string', security: ['bearer'], responses: [new OAT\Response(response: 200, description: 'ok')])]
    public function string(): void
    {
    }

    #[OAT\Get(path: '/list', operationId: 'list', security: [['bearer', 'api_key']], responses: [new OAT\Response(response: 200, description: 'ok')])]
    public function list(): void
    {
    }

    #[OAT\Get(path: '/map', operationId: 'map', security: [['bearer' => []]], responses: [new OAT\Response(response: 200, description: 'ok')])]
    public function map(): void
    {
    }

    #[OAT\Get(path: '/public', operationId: 'public', security: [], responses: [new OAT\Response(response: 200, description: 'ok')])]
    public function public(): void
    {
    }
}
