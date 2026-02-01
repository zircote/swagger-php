<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Polymorphism\Attributes;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'FlResponsible'
)]
final class Fl extends AbstractResponsible
{
    public const TYPE = 'fl';

    #[OAT\Property]
    public ?string $property3 = null;
}
