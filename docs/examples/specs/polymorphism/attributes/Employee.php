<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Polymorphism\Attributes;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'EmployeeResponsible'
)]
final class Employee extends AbstractResponsible
{
    #[OAT\Property(
        property: 'type'
    )]
    protected const TYPE = 'Virtual';

    #[OAT\Property(
        nullable: false
    )]
    public string $property2;
}
