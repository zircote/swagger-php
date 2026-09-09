<?php

namespace Openapi\Snippets\Cookbook\SchemaExamples;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Port',
    type: 'integer',
    examples: [80, 443],
)]
class PortModel
{
}
