<?php

namespace OpenApi\Rector\Tests\Fixture\SecurityStringToRequirement;

use OpenApi\Attributes as OA;

class Other
{
    public function __construct(public array $security = [])
    {
    }
}

class Users
{
    #[OA\Get(path: '/users', security: [['bearerAuth' => []], ['apiKey']])]
    public function users(): void
    {
        new Other(security: ['bearerAuth']);
    }

    #[OA\Get(path: '/public', security: [])]
    public function public(): void
    {
    }
}

?>
