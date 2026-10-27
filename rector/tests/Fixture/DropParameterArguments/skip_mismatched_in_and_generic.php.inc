<?php

namespace OpenApi\Rector\Tests\Fixture\DropParameterArguments;

class Mismatched
{
    #[\OpenApi\Attributes\QueryParameter(name: 'q', in: 'header')]
    #[\OpenApi\Attributes\PathParameter(name: 'p', in: self::IN)]
    #[\OpenApi\Attributes\Parameter(name: 'h', in: 'header', allowEmptyValue: true, style: 'simple')]
    public function list(): void
    {
    }
}

?>
