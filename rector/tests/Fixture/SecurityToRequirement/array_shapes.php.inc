<?php

namespace OpenApi\Rector\Tests\Fixture\SecurityToRequirement;

use OpenApi\Spec as OA;

class Shapes
{
    #[OA\Operation\Get(path: '/map', security: [['bearerAuth' => []]])]
    public function map(): void
    {
    }

    #[OA\Operation\Get(path: '/list', security: [['bearerAuth']])]
    public function list(): void
    {
    }

    #[OA\Operation\Get(path: '/scopes', security: [['oauth2' => ['read', 'write']]])]
    public function scopes(): void
    {
    }

    #[OA\Operation\Get(path: '/and', security: [['oauth2' => ['read'], 'apiKey']])]
    public function both(): void
    {
    }

    #[OA\Operation\Get(path: '/anonymous', security: [[], ['bearerAuth' => []]])]
    public function anonymous(): void
    {
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\SecurityToRequirement;

use OpenApi\Spec as OA;

class Shapes
{
    #[OA\Operation\Get(path: '/map', security: [new \OpenApi\Spec\Security\Requirement(scheme: 'bearerAuth')])]
    public function map(): void
    {
    }

    #[OA\Operation\Get(path: '/list', security: [new \OpenApi\Spec\Security\Requirement(scheme: 'bearerAuth')])]
    public function list(): void
    {
    }

    #[OA\Operation\Get(path: '/scopes', security: [new \OpenApi\Spec\Security\Requirement(scheme: 'oauth2', scopes: ['read', 'write'])])]
    public function scopes(): void
    {
    }

    #[OA\Operation\Get(path: '/and', security: [new \OpenApi\Spec\Security\Requirement(schemes: ['oauth2' => ['read'], 'apiKey'])])]
    public function both(): void
    {
    }

    #[OA\Operation\Get(path: '/anonymous', security: [new \OpenApi\Spec\Security\Requirement(), new \OpenApi\Spec\Security\Requirement(scheme: 'bearerAuth')])]
    public function anonymous(): void
    {
    }
}

?>
