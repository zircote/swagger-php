<?php

namespace OpenApi\Rector\Tests\Fixture;

use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;

#[\OpenApi\Spec\Schema(component: 'Pet')]
class HybridKeepsAnnotationsAlias
{
    public function describe(): ?OA\Schema
    {
        return null;
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture;

use OpenApi\Annotations as OA;
use OpenApi\Spec as OAS;

#[OAS\Schema(component: 'Pet')]
class HybridKeepsAnnotationsAlias
{
    public function describe(): ?OA\Schema
    {
        return null;
    }
}

?>
