<?php

namespace OpenApi\Rector\Tests\Fixture;

use OpenApi\Spec\Schema;

#[Schema(component: 'Pet')]
class PerClassSpecImport
{
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture;

use OpenApi\Spec as OAS;

#[OAS\Schema(component: 'Pet')]
class PerClassSpecImport
{
}

?>
