<?php

namespace OpenApi\Rector\Tests\Fixture\MoveSchemaKeywords;

enum TransportMode: string
{
    case Train = 'train';
}

class SchemaStringClass
{
    #[\OpenApi\Spec\Property(property: 'mode', schema: TransportMode::class, description: 'How the trip was made', nullable: true)]
    public ?TransportMode $mode;
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\MoveSchemaKeywords;

enum TransportMode: string
{
    case Train = 'train';
}

class SchemaStringClass
{
    #[\OpenApi\Spec\Property(property: 'mode', schema: new \OpenApi\Spec\Schema(ref: TransportMode::class, description: 'How the trip was made', nullable: true))]
    public ?TransportMode $mode;
}

?>
