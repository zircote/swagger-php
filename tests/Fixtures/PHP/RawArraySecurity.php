<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Raw array security', version: '1.0')]
#[OA\Security\Scheme\Http(component: 'bearer', scheme: 'bearer')]
#[OA\Security\Scheme\ApiKey(component: 'api_key', name: 'api_key', in: 'header')]
#[OA\Security\Scheme\ApiKey(component: 'other', name: 'other', in: 'header')]
#[OA\PathItem(prefix: '/admin', security: [['other']])]
class RawArraySecurityController
{
    #[OA\Operation\Get(path: '/map', operationId: 'map', security: [['bearer' => []]])]
    #[OA\Response(response: 200, description: 'OK')]
    public function map(): void
    {
    }

    #[OA\Operation\Get(path: '/list', operationId: 'list', security: [['bearer', 'api_key']])]
    #[OA\Response(response: 200, description: 'OK')]
    public function list(): void
    {
    }
}
