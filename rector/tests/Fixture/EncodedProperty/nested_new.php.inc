<?php

namespace OpenApi\Rector\Tests\Fixture\EncodedProperty;

#[\OpenApi\Attributes\Schema(properties: [new \OpenApi\Attributes\Property(property: 'file', encoding: new \OpenApi\Attributes\Encoding(contentType: 'image/png'))])]
class Upload
{
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\EncodedProperty;

#[\OpenApi\Attributes\Schema(properties: [new \OpenApi\Spec\Property\Encoded(property: 'file', encoding: new \OpenApi\Attributes\Encoding(contentType: 'image/png'))])]
class Upload
{
}

?>
