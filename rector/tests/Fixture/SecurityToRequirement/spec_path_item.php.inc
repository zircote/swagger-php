<?php

namespace OpenApi\Rector\Tests\Fixture\SecurityToRequirement;

use OpenApi\Spec as OA;

#[OA\PathItem(prefix: '/admin', security: ['bearerAuth'])]
class Admin
{
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\SecurityToRequirement;

use OpenApi\Spec as OA;

#[OA\PathItem(prefix: '/admin', security: [new \OpenApi\Spec\Security\Requirement(scheme: 'bearerAuth')])]
class Admin
{
}

?>
