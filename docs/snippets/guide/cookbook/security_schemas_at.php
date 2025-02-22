<?php

use OpenApi\Attributes as OA;

#[OA\SecurityScheme(
    type: "apiKey",
    name: "api_key",
    in: "header",
    securityScheme: "api_key",
)]
#[OA\SecurityScheme(
    type: "oauth2",
    securityScheme: "petstore_auth",
    flows: new OA\Flow(
        authorizationUrl: "http://petstore.swagger.io/oauth/dialog",
        flow: "implicit",
        scopes: [
            "read:pets" => "read your pets",
            "write:pets" => "modify pets in your account"
        ],
    ),
)]
class OpenApiSpec
{
}
