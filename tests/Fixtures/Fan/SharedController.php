<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Fan;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Fan', version: '1.0')]
class SharedController
{
    #[OA\Operation\Put(path: '/pets/{id}', operationId: 'updatePet')]
    #[OA\Operation\Delete(path: '/pets/{id}', operationId: 'deletePet')]
    #[OA\Response(response: 404, description: 'No such pet', fan: OA\Fan::All)]
    public function change(
        #[OA\Parameter\Path(name: 'id', fan: OA\Fan::All)]
        int $id,
    ): void {
    }

    #[OA\Operation\Get(path: '/pets', operationId: 'listPets')]
    #[OA\Response(response: 200, description: 'The pets')]
    #[OA\Response(response: 400, description: 'Bad query')]
    #[OA\Header(header: 'X-Rate-Limit', description: 'Requests left', fan: OA\Fan::All)]
    public function list(): void
    {
    }
}
