<?php

namespace OpenApi\Examples\Polymorphism;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="EmployeeResponsible")
 */
final class Employee extends AbstractResponsible
{
    protected const TYPE = 'employee';

    /**
     * @OA\Property(nullable=false)
     *
     * @var string
     */
    public $property2;
}
