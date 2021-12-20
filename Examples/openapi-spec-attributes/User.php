<?php

namespace OpenApi\Examples\OpenapiSpecAttributes;

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
