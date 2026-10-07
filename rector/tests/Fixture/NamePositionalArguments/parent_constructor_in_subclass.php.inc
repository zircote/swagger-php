<?php

namespace OpenApi\Rector\Tests\Fixture\NamePositionalArguments;

use OpenApi\Attributes\Response;

abstract class AbstractResponse extends Response
{
    public function __construct(?string $ref = null, int|string|null $response = null, ?string $description = null, ?array $x = null)
    {
        parent::__construct($ref, $response, $description, x: $x);
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\NamePositionalArguments;

use OpenApi\Attributes\Response;

abstract class AbstractResponse extends Response
{
    public function __construct(?string $ref = null, int|string|null $response = null, ?string $description = null, ?array $x = null)
    {
        parent::__construct(ref: $ref, response: $response, description: $description, x: $x);
    }
}

?>
