<?php

namespace OpenApi\Examples\OpenapiSpecAttributes;

use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'repository')]
class Repository
{

    /**
     * @var string
     */
    #[OAT\Property(type: 'string')]
    public $slug;

    /**
     * @var User
     */
    #[OAT\Property(ref: '#/components/schemas/user')]
    public $owner;
}
