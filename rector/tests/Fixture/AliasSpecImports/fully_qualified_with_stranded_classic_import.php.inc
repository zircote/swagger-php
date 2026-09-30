<?php

namespace OpenApi\Rector\Tests\Fixture\AliasSpecImports;

use OpenApi\Attributes as OAT;

#[\OpenApi\Spec\Schema(component: 'Pet')]
class FullyQualifiedWithStrandedClassicImport
{
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\AliasSpecImports;

use OpenApi\Spec as OAS;

#[OAS\Schema(component: 'Pet')]
class FullyQualifiedWithStrandedClassicImport
{
}

?>
