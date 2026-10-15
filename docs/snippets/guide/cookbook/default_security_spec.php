<?php

namespace Openapi\Snippets\Cookbook\DefaultSecurity;

use OpenApi\Spec as OA;

#[OA\OpenApi]
#[OA\Security\Requirement(scheme: 'bearerAuth')]
#[OA\Security\Scheme\Http(component: 'bearerAuth', scheme: 'bearer')]
abstract class OpenApiSpec
{
}
