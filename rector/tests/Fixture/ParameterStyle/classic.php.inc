<?php

namespace OpenApi\Rector\Tests\Fixture\ParameterStyle;

class Filters
{
    #[\OpenApi\Attributes\QueryParameter(name: 'filter', deepObject: true)]
    #[\OpenApi\Attributes\QueryParameter(name: 'ids', spaceDelimited: ['a'])]
    #[\OpenApi\Attributes\PathParameter(name: 'tags', pipeDelimited: [])]
    #[\OpenApi\Attributes\Get(path: '/a', parameters: [new \OpenApi\Attributes\Parameter(name: 'sort', in: 'query', deepObject: 1)])]
    public function list(): void
    {
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\ParameterStyle;

class Filters
{
    #[\OpenApi\Attributes\QueryParameter(name: 'filter', style: 'deepObject')]
    #[\OpenApi\Attributes\QueryParameter(name: 'ids', style: 'spaceDelimited')]
    #[\OpenApi\Attributes\PathParameter(name: 'tags', style: 'pipeDelimited')]
    #[\OpenApi\Attributes\Get(path: '/a', parameters: [new \OpenApi\Attributes\Parameter(name: 'sort', in: 'query', style: 'deepObject')])]
    public function list(): void
    {
    }
}

?>
