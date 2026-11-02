<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ClassDescriptions;

use OpenApi\Spec as OA;

class AccountController
{
    #[OA\Operation\Post(path: '/accounts', responses: [new OA\Response(response: 201, description: 'Opened')])]
    public function open(#[OA\RequestBody] Account $account): void
    {
    }
}
