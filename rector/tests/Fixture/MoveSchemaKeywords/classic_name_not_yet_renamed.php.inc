<?php

namespace OpenApi\Rector\Tests\Fixture\MoveSchemaKeywords;

class ClassicNameNotYetRenamed
{
    #[\OpenApi\Attributes\Property(property: 'name', type: 'string')]
    public string $name;
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\MoveSchemaKeywords;

class ClassicNameNotYetRenamed
{
    #[\OpenApi\Attributes\Property(property: 'name', schema: new \OpenApi\Spec\Schema(type: 'string'))]
    public string $name;
}

?>
