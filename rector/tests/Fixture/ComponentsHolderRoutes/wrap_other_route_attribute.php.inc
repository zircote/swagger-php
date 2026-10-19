<?php

namespace OpenApi\Rector\Tests\Fixture\ComponentsHolderRoutes;

use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

class PetController
{
    #[Route('/pets', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'The pets')]
    public function list(): void
    {
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\ComponentsHolderRoutes;

use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[\OpenApi\Attributes\Components]
class PetController
{
    #[Route('/pets', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'The pets')]
    public function list(): void
    {
    }
}

?>
