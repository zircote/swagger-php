<?php

namespace OpenApi\Rector\Tests\Fixture\MoveSchemaKeywords;

class SchemaStringPointer
{
    #[\OpenApi\Spec\Property(property: 'summit', schema: '#/components/schemas/Summit', description: 'Summit ID or expanded object')]
    public int $summit;

    #[\OpenApi\Spec\Property(property: 'owner', schema: '#/components/schemas/Member')]
    public int $owner;
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\MoveSchemaKeywords;

class SchemaStringPointer
{
    #[\OpenApi\Spec\Property(property: 'summit', schema: new \OpenApi\Spec\Schema(ref: '#/components/schemas/Summit', description: 'Summit ID or expanded object'))]
    public int $summit;

    #[\OpenApi\Spec\Property(property: 'owner', schema: new \OpenApi\Spec\Schema(ref: '#/components/schemas/Member'))]
    public int $owner;
}

?>
