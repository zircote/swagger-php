<?php

namespace OpenApi\Rector\Tests\Fixture\ClassicToSpecSet;

use OpenApi\Attributes as OA;

class Pets
{
    #[OA\Get(path: '/pets/{id}', operationId: 'getPet')]
    #[OA\Post(path: '/pets/{id}', operationId: 'postPet')]
    #[OA\Response(response: 200, description: 'The pet')]
    #[OA\Header(header: 'X-Request-Id', description: 'Correlates the request')]
    public function pet(#[OA\PathParameter(name: 'id')] string $id): void
    {
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\ClassicToSpecSet;

use OpenApi\Spec as OAS;

class Pets
{
    #[OAS\Operation\Get(path: '/pets/{id}', operationId: 'getPet')]
    #[OAS\Operation\Post(path: '/pets/{id}', operationId: 'postPet')]
    #[OAS\Response(response: 200, description: 'The pet', fan: OAS\Fan::All)]
    #[OAS\Header(header: 'X-Request-Id', description: 'Correlates the request')]
    public function pet(#[OAS\Parameter\Path(name: 'id', fan: OAS\Fan::All)] string $id): void
    {
    }
}

?>
