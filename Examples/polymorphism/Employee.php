<?php

namespace OpenApi\Examples\Polymorphism;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="EmployeeResponsible")
 */
final class Employee extends AbstractResponsible
{
    /**
     * @OA\Property(property="type")
     */
    protected const TYPE = 'Virtual';

    /**
     * @OA\Property(nullable=false)
     *
     * @var string
     */
    public $property2;
}
