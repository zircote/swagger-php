<?php

namespace OpenApi\Rector\Tests\Fixture\MoveSchemaKeywords;

class EncodedPropertyKeepsEncoding
{
    #[\OpenApi\Spec\Property\Encoded(type: 'string', format: 'binary', encoding: new \OpenApi\Spec\Encoding(contentType: 'image/png'))]
    public $file;
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\MoveSchemaKeywords;

class EncodedPropertyKeepsEncoding
{
    #[\OpenApi\Spec\Property\Encoded(encoding: new \OpenApi\Spec\Encoding(contentType: 'image/png'), schema: new \OpenApi\Spec\Schema(type: 'string', format: 'binary'))]
    public $file;
}

?>
