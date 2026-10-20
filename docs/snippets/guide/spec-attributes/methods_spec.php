<?php

namespace Openapi\Snippets\SpecAttributes\Methods;

use OpenApi\Spec as OA;

class PetController
{
    #[OA\Operation(path: '/pets/{id}', operationId: '{method}Pet', methods: [OA\HttpMethod::Put, OA\HttpMethod::Patch])]
    #[OA\Response(response: 204, description: 'Updated')]
    public function update(
        #[OA\Parameter\Path(name: 'id')]
        int $id,
    ) {
    }
}
