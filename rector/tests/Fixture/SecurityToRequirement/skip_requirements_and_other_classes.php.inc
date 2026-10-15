<?php

namespace OpenApi\Rector\Tests\Fixture\SecurityToRequirement;

use OpenApi\Attributes as OA;

class Other
{
    public const SCHEME = 'bearerAuth';

    public const SCOPES = ['read'];

    public function __construct(public array $security = [])
    {
    }
}

class Users
{
    #[OA\Get(path: '/users', security: [new \OpenApi\Spec\Security\Requirement(scheme: 'bearerAuth')])]
    public function users(): void
    {
        new Other(security: ['bearerAuth', ['apiKey' => []]]);
    }

    #[OA\Get(path: '/public', security: [])]
    public function public(): void
    {
    }

    #[OA\Get(path: '/constant', security: [[Other::SCHEME => []], [Other::SCHEME]])]
    public function constant(): void
    {
    }

    #[OA\Get(path: '/scopes', security: [['oauth2' => Other::SCOPES]])]
    public function scopes(): void
    {
    }
}

?>
