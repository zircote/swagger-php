<?php

namespace OpenApi\Rector\Tests\Fixture\EncodingKey;

class Upload
{
    #[\OpenApi\Attributes\MediaType(mediaType: 'multipart/form-data', encoding: [new \OpenApi\Attributes\Encoding(property: 'file', contentType: 'image/png')])]
    #[\OpenApi\Attributes\Encoding(property: 'avatar', contentType: 'image/jpeg')]
    public $file;
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\EncodingKey;

class Upload
{
    #[\OpenApi\Attributes\MediaType(mediaType: 'multipart/form-data', encoding: [new \OpenApi\Attributes\Encoding(encoding: 'file', contentType: 'image/png')])]
    #[\OpenApi\Attributes\Encoding(encoding: 'avatar', contentType: 'image/jpeg')]
    public $file;
}

?>
