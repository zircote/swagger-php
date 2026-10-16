<?php

namespace OpenApi\Rector\Tests\Fixture\ComponentsHolder;

use OpenApi\Attributes as OA;

class Models
{
    #[OA\Schema(schema: 'Thing', type: 'object')]
    public function thing(): void
    {
    }

    #[OA\QueryParameter(parameter: 'page', name: 'page')]
    public function page(): void
    {
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\ComponentsHolder;

use OpenApi\Attributes as OA;

#[\OpenApi\Attributes\Components]
class Models
{
    #[OA\Schema(schema: 'Thing', type: 'object')]
    public function thing(): void
    {
    }

    #[OA\QueryParameter(parameter: 'page', name: 'page')]
    public function page(): void
    {
    }
}

?>
