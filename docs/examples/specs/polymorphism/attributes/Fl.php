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

<<<<<<< HEAD
    #[OAT\Property()]
    public ?string $property3;
=======
    #[OAT\Property]
    public ?string $property3 = null;
>>>>>>> 09b3543 (Subject examples and tests to rector rules (#1942))
}
