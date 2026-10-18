<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\OperationMethods;

use OpenApi\Spec as OA;

class MixedController
{
    #[OA\Operation(path: '/pets/{id}', methods: [OA\HttpMethod::Put, OA\HttpMethod::Patch])]
    #[OA\Operation\Delete(path: '/pets/{id}')]
    #[OA\Response(response: 404, description: 'No such pet')]
    public function change(int $id): void
    {
    }
}
