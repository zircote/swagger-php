<?php

namespace OpenApi\Rector\Tests\Fixture\AliasSpecImports;

use OpenApi\Spec\Schema;

#[Schema(component: 'Pet')]
class PerClassSpecImport
{
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\AliasSpecImports;

use OpenApi\Spec as OAS;

#[OAS\Schema(component: 'Pet')]
class PerClassSpecImport
{
}

?>
