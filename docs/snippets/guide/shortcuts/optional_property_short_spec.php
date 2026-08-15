<?php

namespace Openapi\Snippets\Shortcuts\OptionalProperty;

use OpenApi\Spec as OA;

#[OA\Schema]
class Product
{
    #[OA\Schema(format: 'int64')]
    public int $id;
}
