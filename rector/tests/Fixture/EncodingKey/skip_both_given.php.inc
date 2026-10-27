<?php

namespace OpenApi\Rector\Tests\Fixture\EncodingKey;

class BothGiven
{
    #[\OpenApi\Attributes\Encoding(property: 'file', encoding: 'other')]
    public $file;
}

?>
