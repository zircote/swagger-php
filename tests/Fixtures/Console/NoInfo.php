<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Console;

use OpenApi\Spec as OA;

/**
 * One operation and no `Info`, which `openapi-base.yaml` next to it provides; `openapi-webhooks.yaml` adds a webhook.
 */
class NoInfo
{
    #[OA\Operation\Get(path: '/items')]
    #[OA\Response(response: 200, description: 'OK')]
    public function getItems()
    {
    }
}
