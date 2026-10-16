<?php

namespace OpenApi\Rector\Tests\Fixture\ComponentsHolder;

use OpenApi\Spec as OA;

class Shared
{
    #[OA\Response(component: 'notFound', description: 'Not found')]
    public $notFound;
}

class Promoted
{
    public function __construct(
        #[OA\Schema(component: 'Id', type: 'integer')]
        public int $id,
    ) {
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\ComponentsHolder;

use OpenApi\Spec as OA;

#[\OpenApi\Spec\Components]
class Shared
{
    #[OA\Response(component: 'notFound', description: 'Not found')]
    public $notFound;
}

#[\OpenApi\Spec\Components]
class Promoted
{
    public function __construct(
        #[OA\Schema(component: 'Id', type: 'integer')]
        public int $id,
    ) {
    }
}

?>
