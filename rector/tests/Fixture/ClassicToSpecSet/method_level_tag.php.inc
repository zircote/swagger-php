<?php

namespace OpenApi\Rector\Tests\Fixture\ClassicToSpecSet;

use OpenApi\Attributes as OA;

final class PetController
{
    #[OA\Tag(name: 'pets', description: 'Everything about pets')]
    #[OA\Get(path: '/pets')]
    #[OA\Response(response: 200, description: 'The pets')]
    public function list(): void
    {
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\ClassicToSpecSet;

use OpenApi\Spec as OAS;

final class PetController
{
    #[OAS\Tag(name: 'pets', description: 'Everything about pets')]
    #[OAS\Operation\Get(path: '/pets')]
    #[OAS\Response(response: 200, description: 'The pets')]
    public function list(): void
    {
    }
}

?>
