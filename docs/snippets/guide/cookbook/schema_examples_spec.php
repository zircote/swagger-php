<?php

namespace Openapi\Snippets\Cookbook\SchemaExamples;

use OpenApi\Spec as OA;

#[OA\Schema(
    schema: 'Port',
    type: 'integer',
    examples: [80, 443],
)]
class PortModel
{
}
