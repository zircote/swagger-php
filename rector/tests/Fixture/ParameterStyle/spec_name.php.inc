<?php

namespace OpenApi\Rector\Tests\Fixture\ParameterStyle;

class SpecName
{
    #[\OpenApi\Spec\Parameter\Query(name: 'filter', deepObject: true)]
    public function list(): void
    {
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\ParameterStyle;

class SpecName
{
    #[\OpenApi\Spec\Parameter\Query(name: 'filter', style: 'deepObject')]
    public function list(): void
    {
    }
}

?>
