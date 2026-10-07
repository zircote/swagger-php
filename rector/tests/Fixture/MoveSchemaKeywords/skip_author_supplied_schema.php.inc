<?php

namespace OpenApi\Rector\Tests\Fixture\MoveSchemaKeywords;

class SkipAuthorSuppliedSchema
{
    #[\OpenApi\Spec\Property(property: 'own', type: 'string', schema: new \OpenApi\Spec\Schema(format: 'date'))]
    public string $own;
}

?>
