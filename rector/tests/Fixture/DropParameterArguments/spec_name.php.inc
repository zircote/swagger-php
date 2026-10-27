<?php

namespace OpenApi\Rector\Tests\Fixture\DropParameterArguments;

class SpecName
{
    #[\OpenApi\Spec\Parameter\Header(name: 'h', in: 'header', style: 'simple')]
    public function list(): void
    {
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\DropParameterArguments;

class SpecName
{
    #[\OpenApi\Spec\Parameter\Header(name: 'h')]
    public function list(): void
    {
    }
}

?>
