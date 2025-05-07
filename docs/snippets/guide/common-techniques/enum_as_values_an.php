<?php

use OpenApi\Annotations as OA;

enum Suit
{
    case Hearts;
    case Diamonds;
    case Clubs;
    case Spades;
}

class Model
{
    /**
     * @OA\Property(enum={Suit::Hearts, Suit::Diamonds})
     */
    protected array $someSuits;
}
