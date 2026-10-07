<?php

namespace OpenApi\Rector\Tests\Fixture\NamePositionalArguments;

use OpenApi\Attributes as OA;

class Controller
{
    #[OA\Get(path: '/x', responses: [new OA\Response(null, 200, 'ok')])]
    #[OA\Response(null, 404)]
    public function get(): void
    {
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\NamePositionalArguments;

use OpenApi\Attributes as OA;

class Controller
{
    #[OA\Get(path: '/x', responses: [new OA\Response(ref: null, response: 200, description: 'ok')])]
    #[OA\Response(ref: null, response: 404)]
    public function get(): void
    {
    }
}

?>
