<?php

namespace OpenApi\Rector\Tests\Fixture\EncodedProperty;

class NoEncoding
{
    #[\OpenApi\Attributes\Property(type: 'string', format: 'binary')]
    public $file;
}

?>
