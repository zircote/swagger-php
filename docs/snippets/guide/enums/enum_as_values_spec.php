<?php

namespace Openapi\Snippets\Enums\Value;

use OpenApi\Spec as OA;

enum Suit
{
    case Hearts;
    case Diamonds;
    case Clubs;
    case Spades;
}

class Model
{
    #[OA\Property]
    #[OA\Schema(enum: [Suit::Hearts, Suit::Diamonds])]
    protected array $someSuits;
}
