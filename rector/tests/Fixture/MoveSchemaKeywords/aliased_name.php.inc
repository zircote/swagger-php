<?php

namespace OpenApi\Rector\Tests\Fixture\MoveSchemaKeywords;

use OpenApi\Spec as OAS;

class AliasedName
{
    #[OAS\Property(property: 'name', type: 'string')]
    public string $name;
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\MoveSchemaKeywords;

use OpenApi\Spec as OAS;

class AliasedName
{
    #[OAS\Property(property: 'name', schema: new \OpenApi\Spec\Schema(type: 'string'))]
    public string $name;
}

?>
