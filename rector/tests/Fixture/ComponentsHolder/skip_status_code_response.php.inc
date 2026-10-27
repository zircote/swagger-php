<?php

namespace OpenApi\Rector\Tests\Fixture\ComponentsHolder;

use OpenApi\Attributes as OA;

class RoutedElsewhere
{
    #[OA\Response(response: 200, description: 'A ok')]
    #[OA\Response(response: '404', description: 'A missing')]
    public function one(): void
    {
    }

    #[OA\Response(response: '4XX', description: 'B failed')]
    #[OA\Response(response: 'default', description: 'B otherwise')]
    public function two(): void
    {
    }
}

?>
