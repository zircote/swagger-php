<?php

namespace OpenApi\Rector\Tests\Fixture\ComponentsHolderRoutes;

use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Attribute\Route;

class PetController
{
    #[Route('/pets', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'The pets')]
    public function list(): void
    {
    }

    #[\Symfony\Component\Routing\Attribute\Route('/pets/{id}', methods: ['DELETE'])]
    #[OA\Response(response: 404, description: 'No such pet')]
    public function delete(): void
    {
    }
}
