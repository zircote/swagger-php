<?php

namespace OpenApi\Examples\UsingLinksPhp81;

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
    public $uuid;
}
