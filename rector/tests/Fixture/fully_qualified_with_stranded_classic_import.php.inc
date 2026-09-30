<?php

namespace OpenApi\Rector\Tests\Fixture;

use OpenApi\Attributes as OAT;

#[\OpenApi\Spec\Schema(component: 'Pet')]
class FullyQualifiedWithStrandedClassicImport
{
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture;

use OpenApi\Spec as OAS;

#[OAS\Schema(component: 'Pet')]
class FullyQualifiedWithStrandedClassicImport
{
}

?>
