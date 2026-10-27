<?php

namespace OpenApi\Rector\Tests\Fixture\ComponentsHolder;

use OpenApi\Attributes as OA;

class Responses
{
    #[OA\Response(response: 200, description: 'A ok')]
    public function one(): void
    {
    }

    #[OA\Response(response: 'NotFound', description: 'Not found')]
    public function notFound(): void
    {
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\ComponentsHolder;

use OpenApi\Attributes as OA;

#[\OpenApi\Attributes\Components]
class Responses
{
    #[OA\Response(response: 200, description: 'A ok')]
    public function one(): void
    {
    }

    #[OA\Response(response: 'NotFound', description: 'Not found')]
    public function notFound(): void
    {
    }
}

?>
