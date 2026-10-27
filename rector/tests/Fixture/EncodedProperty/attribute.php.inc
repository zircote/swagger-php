<?php

namespace OpenApi\Rector\Tests\Fixture\EncodedProperty;

class Upload
{
    #[\OpenApi\Attributes\Property(type: 'string', encoding: new \OpenApi\Attributes\Encoding(contentType: 'image/png'))]
    public $file;
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\EncodedProperty;

class Upload
{
    #[\OpenApi\Spec\Property\Encoded(type: 'string', encoding: new \OpenApi\Attributes\Encoding(contentType: 'image/png'))]
    public $file;
}

?>
