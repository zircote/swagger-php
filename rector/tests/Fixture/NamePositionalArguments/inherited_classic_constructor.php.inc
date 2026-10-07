<?php

namespace OpenApi\Rector\Tests\Fixture\NamePositionalArguments;

abstract class Stock extends \OpenApi\Attributes\Response
{
}

final class NotFound extends Stock
{
    public function __construct()
    {
        // Stock declares no constructor, so this is classic Response's
        parent::__construct(null, 404);
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\NamePositionalArguments;

abstract class Stock extends \OpenApi\Attributes\Response
{
}

final class NotFound extends Stock
{
    public function __construct()
    {
        // Stock declares no constructor, so this is classic Response's
        parent::__construct(ref: null, response: 404);
    }
}

?>
