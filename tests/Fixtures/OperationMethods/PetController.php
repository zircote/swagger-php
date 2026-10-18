<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\OperationMethods;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Operation methods', version: '1.0')]
class PetController
{
    #[OA\Operation(path: '/pets/{id}', operationId: '{method}Pet', methods: [OA\HttpMethod::Get, 'HEAD'])]
    #[OA\Response(response: 200, description: 'The pet')]
    #[OA\Response(response: 404, description: 'No such pet')]
    public function show(
        #[OA\Parameter\Path(name: 'id')]
        int $id,
    ): void {
    }

    #[OA\Operation(path: '/pets/{id}', operationId: 'updatePet', methods: [OA\HttpMethod::Put, OA\HttpMethod::Patch])]
    #[OA\Response(response: 204, description: 'Updated')]
    public function update(int $id): void
    {
    }

    #[OA\Operation\Get(path: '/pets', operationId: '{method}Pets')]
    #[OA\Response(response: 200, description: 'The pets')]
    public function list(): void
    {
    }
}
