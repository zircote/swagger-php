<?php

namespace OpenApi\Examples\Polymorphism;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Responsible",
 *     @OA\Discriminator(
 *         propertyName="type",
 *         mapping={
 *             "fl": "#/components/schemas/FlResponsible",
 *             "employee": "#/components/schemas/EmployeeResponsible"
 *         }
 *     ),
 *     oneOf={
 *         @OA\Schema(ref="#/components/schemas/FlResponsible"),
 *         @OA\Schema(ref="#/components/schemas/EmployeeResponsible")
 *     }
 * )
 */
abstract class AbstractResponsible
{
    protected const TYPE = null;

    /**
     * @OA\Property(nullable=false, enum={"employee", "assignee", "fl"})
     *
     * @var string
     */
    protected $type;

    public function __construct()
    {
        $this->type = static::TYPE;
    }
}
