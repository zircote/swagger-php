<?php

namespace OpenApi\Rector\Tests\Fixture\MoveSchemaKeywords;

// a bare name is classic's component key, which a spec Property cannot be, and a `ref:` beside
// a reference leaves two to choose from: neither is a guess this rule makes
class SkipSchemaStringNotAReference
{
    #[\OpenApi\Spec\Property(property: 'named', schema: 'Named', type: 'string')]
    public string $named;

    #[\OpenApi\Spec\Property(property: 'both', schema: '#/components/schemas/A', ref: '#/components/schemas/B')]
    public string $both;
}

?>
