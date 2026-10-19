<?php

namespace Openapi\Snippets\SpecAttributes\Fan;

use OpenApi\Spec as OA;

class PetController
{
    #[OA\Operation\Put(path: '/pets/{id}')]
    #[OA\Operation\Delete(path: '/pets/{id}')]
    #[OA\Response(response: 404, description: 'Not found', fan: OA\Fan::All)]
    public function change(
        #[OA\Parameter\Path(name: 'id', fan: OA\Fan::All)]
        int $id,
    ) {
    }
}
