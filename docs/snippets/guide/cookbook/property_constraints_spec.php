<?php

namespace Openapi\Snippets\Cookbook\PropertyConstraints;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'User')]
class User
{
    #[OA\Schema(maxLength: 50)]
    public string $name;

    #[OA\Schema(format: 'email')]
    public string $email;
}
