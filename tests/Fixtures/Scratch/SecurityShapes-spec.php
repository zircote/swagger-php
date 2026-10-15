<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Security shapes', version: '1.0')]
#[OA\Security\Scheme\Http(securityScheme: 'bearer', scheme: 'bearer')]
#[OA\Security\Scheme\ApiKey(securityScheme: 'api_key', name: 'api_key', in: 'header')]
class SecurityShapesSpec
{
    // a bare scheme name in classic, as the rector set rewrites it
    #[OA\Operation\Get(path: '/string', operationId: 'string', security: [new OA\Security\Requirement(scheme: 'bearer')])]
    #[OA\Response(response: 200, description: 'ok')]
    public function string(): void
    {
    }

    #[OA\Operation\Get(path: '/list', operationId: 'list', security: [new OA\Security\Requirement(schemes: ['bearer', 'api_key'])])]
    #[OA\Response(response: 200, description: 'ok')]
    public function list(): void
    {
    }

    #[OA\Operation\Get(path: '/map', operationId: 'map', security: [new OA\Security\Requirement(schemes: ['bearer' => []])])]
    #[OA\Response(response: 200, description: 'ok')]
    public function map(): void
    {
    }

    #[OA\Operation\Get(path: '/public', operationId: 'public', security: [])]
    #[OA\Response(response: 200, description: 'ok')]
    public function public(): void
    {
    }
}
