<?php

namespace OpenApi\Rector\Tests\Fixture\AliasSpecImports;

use OpenApi\Spec;

#[Spec\Schema(component: 'Pet')]
class BareSpecImport
{
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\AliasSpecImports;

use OpenApi\Spec as OAS;

#[OAS\Schema(component: 'Pet')]
class BareSpecImport
{
}

?>
