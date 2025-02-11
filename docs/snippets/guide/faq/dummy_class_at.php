<?php

use OpenApi\Attributes as OA;

#[OA\Tag(name: "user", description: "User related operations")]
#[OA\Info(
    version: "1.0",
    title: "Example API",
    description: "Example info",
    contact: new OA\Contact(name: "Swagger API Team"),
)]
#[OA\Server(
    url: "https://example.localhost",
    description: "API server",
)]
class OpenApiSpec
{
}
