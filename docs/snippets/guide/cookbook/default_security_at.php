<?php

namespace Openapi\Snippets\Cookbook\DefaultSecurity;

use OpenApi\Attributes as OAT;

#[OAT\OpenApi(
    security: [['bearerAuth' => []]]
)]
#[OAT\Components(
    securitySchemes: [
        new OAT\SecurityScheme(
            securityScheme: 'bearerAuth',
            type: 'http',
            scheme: 'bearer'
        ),
    ]
)]
class OpenApiSpec
{
}
