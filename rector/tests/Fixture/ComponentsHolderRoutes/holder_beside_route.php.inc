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

    #[OA\Schema(schema: 'Pet', type: 'object')]
    public function pet(): void
    {
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\ComponentsHolderRoutes;

use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Attribute\Route;

#[\OpenApi\Attributes\Components]
class PetController
{
    #[Route('/pets', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'The pets')]
    public function list(): void
    {
    }

    #[OA\Schema(schema: 'Pet', type: 'object')]
    public function pet(): void
    {
    }
}

?>
