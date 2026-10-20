<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\OperationMethods;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Invokable', version: '1.0')]
#[OA\Operation(path: '/pets/{id}', methods: [OA\HttpMethod::Put, OA\HttpMethod::Patch])]
#[OA\Response(response: 204, description: 'Updated')]
class InvokableController
{
    public function __invoke(
        #[OA\Parameter\Path(name: 'id')]
        int $id,
    ): void {
    }
}
