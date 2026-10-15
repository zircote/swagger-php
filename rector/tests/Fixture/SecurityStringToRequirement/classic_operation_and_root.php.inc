<?php

namespace OpenApi\Rector\Tests\Fixture\SecurityStringToRequirement;

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

namespace OpenApi\Rector\Tests\Fixture\SecurityStringToRequirement;

use OpenApi\Attributes as OA;

#[OA\OpenApi(security: [new \OpenApi\Spec\Security\Requirement(scheme: 'apiKey')])]
class Api
{
    #[OA\Get(path: '/users', security: [new \OpenApi\Spec\Security\Requirement(scheme: 'bearerAuth'), ['apiKey' => []]])]
    public function users(): void
    {
    }
}

?>
