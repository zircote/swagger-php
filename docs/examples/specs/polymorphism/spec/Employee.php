<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Polymorphism\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'EmployeeResponsible')]
final class Employee extends AbstractResponsible
{
    #[OA\Property(property: 'type')]
    protected const TYPE = 'Virtual';

    #[OA\Property(property: 'property2')]
    #[OA\Schema(nullable: false)]
    public string $property2;
}
