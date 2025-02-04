<?php

use OpenApi\Attributes as OAT;

#[OAT\Tag(name: "user", description: "User related operations")]
#[OAT\Info(
    version: "1.0",
    title: "Example API",
    description: "Example info",
    contact: new OAT\Contact(name: "Swagger API Team"),
)]
#[OAT\Server(
    url: "https://example.localhost",
    description: "API server",
)]
class OpenApiSpec
{
}
