<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Polymorphism\Attributes;

use OpenApi\Attributes as OAT;

#[OAT\Schema()]
final class Request
{
    protected const TYPE = 'employee';

    #[OAT\Property()]
    public AbstractResponsible $payload;
}
