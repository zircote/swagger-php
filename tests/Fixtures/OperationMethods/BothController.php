<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\OperationMethods;

use OpenApi\Spec as OA;

class BothController
{
    #[OA\Operation(path: '/pets/{id}', method: OA\HttpMethod::Get, methods: [OA\HttpMethod::Head])]
    public function show(int $id): void
    {
    }
}
