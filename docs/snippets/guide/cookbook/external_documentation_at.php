<?php

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    externalDocs: new OA\ExternalDocumentation(
        description: "More documentation here...",
        url: "https://example.com/externaldoc1/",
    ),
)]
class OpenApiSpec
{
}
