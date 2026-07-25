<?php

namespace Openapi\Snippets\Cookbook\DefaultSecurity;

use OpenApi\Spec as OA;

#[OA\OpenApi]
#[OA\Security\Requirement(scheme: 'bearerAuth')]
#[OA\Security\Scheme\Http(securityScheme: 'bearerAuth', scheme: 'bearer')]
abstract class OpenApiSpec
{
}
