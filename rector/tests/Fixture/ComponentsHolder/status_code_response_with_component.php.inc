<?php

namespace OpenApi\Rector\Tests\Fixture\ComponentsHolder;

use OpenApi\Spec as OAS;

class ExplicitComponent
{
    #[OAS\Response(component: 'Ok', response: 200, description: 'Ok')]
    public function ok(): void
    {
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\ComponentsHolder;

use OpenApi\Spec as OAS;

#[\OpenApi\Spec\Components]
class ExplicitComponent
{
    #[OAS\Response(component: 'Ok', response: 200, description: 'Ok')]
    public function ok(): void
    {
    }
}

?>
