<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ComponentKey;

use OpenApi\Spec as OA;

// A keyed header is a root on its own: no `Components` wrapper, no sibling to merge into.
#[OA\Header(component: 'RateLimit', description: 'Requests left', schema: new OA\Schema(type: 'integer'))]
class StandaloneHeader
{
}

#[OA\Info(title: 'StandaloneHeader', version: '1.0')]
#[OA\Operation\Get(path: '/things', responses: [
    new OA\Response(response: 200, description: 'Things', headers: [
        new OA\Header(header: 'X-Rate-Limit', ref: '#/components/headers/RateLimit'),
    ]),
])]
class StandaloneHeaderController
{
}
