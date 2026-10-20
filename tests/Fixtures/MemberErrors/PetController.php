<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\MemberErrors;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Member errors', version: '1.0')]
class PetController
{
    #[OA\Operation\Get(path: '/pets', operationId: 'listPets')]
    #[OA\Response(response: 200, description: 'The pets')]
    public function list(): void
    {
    }

    /**
     * The path parameter could go into either operation.
     */
    #[OA\Operation\Put(path: '/pets/{id}')]
    #[OA\Operation\Delete(path: '/pets/{id}')]
    #[OA\Response(response: 204, description: 'Done', fan: OA\Fan::All)]
    public function change(
        #[OA\Parameter\Path(name: 'id')]
        int $id,
    ): void {
    }
}
