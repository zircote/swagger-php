<?php

namespace OpenApi\Examples\UsingLinksPhp81;

use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'repository')]
class Repository
{

    /**
     * @var string
     */
    #[OAT\Property(type: 'string')]
    public $slug;

    #[OAT\Property()]
    public User $owner;
}
