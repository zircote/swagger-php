<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP\SkippedClasses;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Skipped classes', version: '1.0')]
#[OA\Operation\Get(path: '/endpoint', operationId: 'getEndpoint', responses: [
    new OA\Response(response: 200, description: 'OK'),
])]
class Endpoint
{
}
