<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Fan;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Fan', version: '1.0')]
class DeepController
{
    #[OA\Operation\Put(path: '/pets/{id}', operationId: 'updatePet')]
    #[OA\Operation\Patch(path: '/pets/{id}', operationId: 'patchPet')]
    #[OA\Response(response: 200, description: 'The pet', fan: OA\Fan::All)]
    #[OA\MediaType(mediaType: 'application/json')]
    #[OA\Schema\Items(type: 'string', minItems: 1, fan: OA\Fan::Single)]
    public function change(): void
    {
    }
}
