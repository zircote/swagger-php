<?php

namespace Openapi\Snippets\Enums\BackedValueAsSchema;

use OpenApi\Attributes as OA;

#[OA\Schema(type: 'integer')]
enum Colour: int
{
    case GREEN = 1;
    case BLUE = 2;
    case RED = 3;
}

#[OA\Schema()]
class Product
{
    #[OA\Property()]
    public Colour $colour;
}
