<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingLinks\Attributes;

use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'repository')]
class Repository
{
    /**
     * @var string
     */
    #[OAT\Property(type: 'string')]
    public $slug;

    #[OAT\Property]
    public User $owner;
}
