<?php

namespace OpenApi\Rector\Tests\Fixture\DropParameterArguments;

class Params
{
    #[\OpenApi\Attributes\QueryParameter(name: 'q', in: 'query', allowEmptyValue: true, allowReserved: true)]
    #[\OpenApi\Attributes\PathParameter(name: 'p', in: 'path', allowEmptyValue: true, allowReserved: true)]
    #[\OpenApi\Attributes\HeaderParameter(name: 'h', in: 'header', style: 'simple', allowEmptyValue: true, allowReserved: true)]
    #[\OpenApi\Attributes\CookieParameter(name: 'c', in: 'cookie', style: 'form', deepObject: true, spaceDelimited: [], pipeDelimited: [])]
    #[\OpenApi\Attributes\Get(path: '/a', responses: [new \OpenApi\Attributes\Response(response: 200, description: 'ok', headers: [new \OpenApi\Attributes\Header(header: 'X', allowEmptyValue: true)])])]
    public function list(): void
    {
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\DropParameterArguments;

class Params
{
    #[\OpenApi\Attributes\QueryParameter(name: 'q', allowEmptyValue: true, allowReserved: true)]
    #[\OpenApi\Attributes\PathParameter(name: 'p')]
    #[\OpenApi\Attributes\HeaderParameter(name: 'h')]
    #[\OpenApi\Attributes\CookieParameter(name: 'c')]
    #[\OpenApi\Attributes\Get(path: '/a', responses: [new \OpenApi\Attributes\Response(response: 200, description: 'ok', headers: [new \OpenApi\Attributes\Header(header: 'X')])])]
    public function list(): void
    {
    }
}

?>
