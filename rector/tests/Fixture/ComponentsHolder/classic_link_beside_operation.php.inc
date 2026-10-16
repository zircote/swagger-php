<?php

namespace OpenApi\Rector\Tests\Fixture\ComponentsHolder;

use OpenApi\Attributes as OA;

class Owners
{
    #[OA\Get(path: '/owners/{id}', operationId: 'getOwner', responses: [new OA\Response(response: 200, description: 'The owner')])]
    #[OA\Link(link: 'WidgetOwner', operationId: 'getOwner')]
    public function getOwner(): void
    {
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\ComponentsHolder;

use OpenApi\Attributes as OA;

#[\OpenApi\Attributes\Components]
class Owners
{
    #[OA\Get(path: '/owners/{id}', operationId: 'getOwner', responses: [new OA\Response(response: 200, description: 'The owner')])]
    #[OA\Link(link: 'WidgetOwner', operationId: 'getOwner')]
    public function getOwner(): void
    {
    }
}

?>
