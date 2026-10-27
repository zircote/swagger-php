<?php

namespace OpenApi\Rector\Tests\Fixture\EncodingKey;

class SpecName
{
    #[\OpenApi\Spec\Encoding(property: 'file', contentType: 'image/png')]
    public $file;
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\EncodingKey;

class SpecName
{
    #[\OpenApi\Spec\Encoding(encoding: 'file', contentType: 'image/png')]
    public $file;
}

?>
