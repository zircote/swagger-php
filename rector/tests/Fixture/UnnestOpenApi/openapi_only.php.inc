<?php

namespace OpenApi\Rector\Tests\Fixture\UnnestOpenApi;

#[\OpenApi\Attributes\OpenApi(openapi: '3.1.0')]
class VersionOnly
{
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\UnnestOpenApi;

#[\OpenApi\Attributes\OpenApi(version: '3.1.0')]
class VersionOnly
{
}

?>
