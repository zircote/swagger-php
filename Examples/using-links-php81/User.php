<?php

namespace OpenApi\Examples\UsingLinksPhp81;

use JetBrains\PhpStorm\ArrayShape;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'user')]
class User
{

    /**
     * @var string
     */
    #[OAT\Property(type: 'string')]
    public $username;

    /**
     * @var string
     */
    #[OAT\Property(type: 'string')]
    #[MyAttribute]
    public $uuid;

    #[ArrayShape(['ping' => 'pong'])]
    public array $arrayShape;
}
