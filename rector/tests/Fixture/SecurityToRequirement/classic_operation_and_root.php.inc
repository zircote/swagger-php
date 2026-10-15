<?php

namespace OpenApi\Rector\Tests\Fixture\SecurityToRequirement;

use OpenApi\Attributes as OA;

#[OA\OpenApi(security: ['apiKey'])]
class Api
{
    #[OA\Get(path: '/users', security: ['bearerAuth', ['apiKey' => []]])]
    public function users(): void
    {
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\SecurityToRequirement;

use OpenApi\Attributes as OA;

#[OA\OpenApi(security: [new \OpenApi\Spec\Security\Requirement(scheme: 'apiKey')])]
class Api
{
    #[OA\Get(path: '/users', security: [new \OpenApi\Spec\Security\Requirement(scheme: 'bearerAuth'), new \OpenApi\Spec\Security\Requirement(scheme: 'apiKey')])]
    public function users(): void
    {
    }
}

?>
