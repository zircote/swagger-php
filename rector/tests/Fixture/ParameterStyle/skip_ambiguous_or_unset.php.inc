<?php

namespace OpenApi\Rector\Tests\Fixture\ParameterStyle;

class Ambiguous
{
    #[\OpenApi\Attributes\QueryParameter(name: 'a', style: 'form', deepObject: true)]
    #[\OpenApi\Attributes\QueryParameter(name: 'b', deepObject: true, spaceDelimited: [])]
    #[\OpenApi\Attributes\QueryParameter(name: 'c', deepObject: false)]
    #[\OpenApi\Attributes\QueryParameter(name: 'd', deepObject: null)]
    #[\OpenApi\Attributes\HeaderParameter(name: 'e', deepObject: true)]
    #[\OpenApi\Attributes\CookieParameter(name: 'f', deepObject: true)]
    public function list(): void
    {
    }
}

?>
