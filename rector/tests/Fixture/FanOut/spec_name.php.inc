<?php

namespace OpenApi\Rector\Tests\Fixture\FanOut;

use OpenApi\Spec as OAS;

class Risk
{
    #[OAS\Operation\Get(path: '/risk')]
    #[OAS\Operation\Post(path: '/risk')]
    #[OAS\Response(response: 200, description: 'OK')]
    #[OAS\Response(response: 400, description: 'Bad request')]
    #[OAS\Header(header: 'X-Trace', description: 'Trace id')]
    public function analyze(): void
    {
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\FanOut;

use OpenApi\Spec as OAS;

class Risk
{
    #[OAS\Operation\Get(path: '/risk')]
    #[OAS\Operation\Post(path: '/risk')]
    #[OAS\Response(response: 200, description: 'OK', fan: \OpenApi\Spec\Fan::All)]
    #[OAS\Response(response: 400, description: 'Bad request', fan: \OpenApi\Spec\Fan::All)]
    #[OAS\Header(header: 'X-Trace', description: 'Trace id', fan: \OpenApi\Spec\Fan::All)]
    public function analyze(): void
    {
    }
}

?>
