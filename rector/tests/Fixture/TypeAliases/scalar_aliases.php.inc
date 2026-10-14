<?php

namespace OpenApi\Rector\Tests\Fixture\TypeAliases;

class ScalarAliases
{
    #[\OpenApi\Attributes\Property(property: 'id', type: 'long')]
    public int $id;

    #[\OpenApi\Attributes\Property(property: 'count', type: 'int')]
    public int $count;

    #[\OpenApi\Attributes\Property(property: 'active', type: 'Bool')]
    public bool $active;

    #[\OpenApi\Attributes\Property(property: 'ratio', type: 'float', format: 'double')]
    public float $ratio;

    #[\OpenApi\Attributes\Property(property: 'born', schema: new \OpenApi\Spec\Schema(type: 'date'))]
    public string $born;

    #[\OpenApi\Attributes\Property(property: 'seen', type: '\DateTimeImmutable')]
    public \DateTimeImmutable $seen;
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\TypeAliases;

class ScalarAliases
{
    #[\OpenApi\Attributes\Property(property: 'id', type: 'integer', format: 'int64')]
    public int $id;

    #[\OpenApi\Attributes\Property(property: 'count', type: 'integer')]
    public int $count;

    #[\OpenApi\Attributes\Property(property: 'active', type: 'boolean')]
    public bool $active;

    #[\OpenApi\Attributes\Property(property: 'ratio', type: 'number', format: 'double')]
    public float $ratio;

    #[\OpenApi\Attributes\Property(property: 'born', schema: new \OpenApi\Spec\Schema(type: 'string', format: 'date'))]
    public string $born;

    #[\OpenApi\Attributes\Property(property: 'seen', type: 'string', format: 'date-time')]
    public \DateTimeImmutable $seen;
}

?>
