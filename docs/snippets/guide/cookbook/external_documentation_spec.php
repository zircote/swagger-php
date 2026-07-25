<?php

namespace Openapi\Snippets\Cookbook\ExternalDocumentation;

use OpenApi\Spec as OA;

#[OA\ExternalDocumentation(
    description: 'More documentation here...',
    url: 'https://example.com/externaldoc1/',
)]
class OpenApiSpec
{
}
