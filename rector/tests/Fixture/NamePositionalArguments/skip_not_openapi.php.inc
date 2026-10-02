<?php

namespace OpenApi\Rector\Tests\Fixture\NamePositionalArguments;

class Base
{
    public function __construct(public ?string $a = null)
    {
    }
}

final class Child extends Base
{
    public function __construct()
    {
        parent::__construct('a');
        new \ArrayObject([]);
    }
}
