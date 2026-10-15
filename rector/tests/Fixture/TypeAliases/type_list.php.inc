<?php

namespace OpenApi\Rector\Tests\Fixture\TypeAliases;

class TypeList
{
    #[\OpenApi\Spec\Schema(type: ['long', 'null'])]
    public ?int $id;
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\TypeAliases;

class TypeList
{
    #[\OpenApi\Spec\Schema(type: ['integer', 'null'])]
    public ?int $id;
}

?>
