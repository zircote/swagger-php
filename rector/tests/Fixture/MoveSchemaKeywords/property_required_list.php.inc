<?php

namespace OpenApi\Rector\Tests\Fixture\MoveSchemaKeywords;

class PropertyRequiredList
{
    #[\OpenApi\Attributes\Property(property: 'address', type: 'object', required: ['street'])]
    public array $address;
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\MoveSchemaKeywords;

class PropertyRequiredList
{
    #[\OpenApi\Attributes\Property(property: 'address', schema: new \OpenApi\Spec\Schema(type: 'object', required: ['street']))]
    public array $address;
}

?>
