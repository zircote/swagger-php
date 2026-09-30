<?php

namespace OpenApi\Rector\Tests\Fixture\MoveSchemaKeywords;

class PropertyKeywords
{
    #[\OpenApi\Spec\Property(property: 'name', type: 'string', nullable: true)]
    public string $name;
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\MoveSchemaKeywords;

class PropertyKeywords
{
    #[\OpenApi\Spec\Property(property: 'name', schema: new \OpenApi\Spec\Schema(type: 'string', nullable: true))]
    public string $name;
}

?>
