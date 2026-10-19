<?php

namespace OpenApi\Rector\Tests\Fixture\ClassicToSpecSetRoutes;

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

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\ClassicToSpecSetRoutes;

use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Spec as OAS;

class PetController
{
    #[Route('/pets', methods: ['GET'])]
    #[OAS\Response(response: 200, description: 'The pets')]
    public function list(): void
    {
    }

    #[\Symfony\Component\Routing\Attribute\Route('/pets/{id}', methods: ['DELETE'])]
    #[OAS\Response(response: 404, description: 'No such pet')]
    public function delete(): void
    {
    }
}

?>
