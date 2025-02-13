<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingLinks\Attributes;

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
