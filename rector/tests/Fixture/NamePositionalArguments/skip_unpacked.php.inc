<?php

namespace OpenApi\Rector\Tests\Fixture\NamePositionalArguments;

use OpenApi\Attributes\Response;

final class Spread extends Response
{
    public function __construct(mixed ...$args)
    {
        parent::__construct(...$args);
    }
}
