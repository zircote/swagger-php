<?php

namespace OpenApi\Rector\Tests\Fixture\TypeAliases;

// valid types, names outside the map, the two entries with no OpenAPI type, and classes
// outside OpenApi are all left as written
class SkipNotAnAlias
{
    #[\OpenApi\Attributes\Property(property: 'name', type: 'string')]
    public string $name;

    #[\OpenApi\Attributes\Property(property: 'kind', type: 'enum')]
    public string $kind;

    #[\OpenApi\Attributes\Property(property: 'any', type: 'mixed')]
    public mixed $any;

    #[\OpenApi\Attributes\Property(property: 'raw', type: 'byte')]
    public string $raw;

    #[\OpenApi\Attributes\Property(property: 'member', type: Member::class)]
    public Member $member;

    #[\Other\Property(type: 'int')]
    public int $other;
}

?>
