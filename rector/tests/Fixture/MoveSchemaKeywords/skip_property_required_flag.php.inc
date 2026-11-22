<?php

namespace OpenApi\Rector\Tests\Fixture\MoveSchemaKeywords;

class SkipPropertyRequiredFlag
{
    #[\OpenApi\Spec\Property(property: 'nickname', required: true)]
    public ?string $nickname;
}

?>
