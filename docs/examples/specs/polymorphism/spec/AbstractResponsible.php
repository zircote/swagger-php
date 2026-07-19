<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Polymorphism\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'Responsible', oneOf: [
    new OA\Schema(ref: Fl::class),
    new OA\Schema(ref: Employee::class),
], discriminator: new OA\Discriminator(
    propertyName: 'type',
    mapping: [
        'fl' => Fl::class,
        'employee' => Employee::class,
    ],
))]
abstract class AbstractResponsible
{
    protected const TYPE = null;

    #[OA\Property(property: 'type')]
    #[OA\Schema(nullable: false, enum: ['employee', 'assignee', 'fl'])]
    protected string $type;

    public function __construct()
    {
        $this->type = static::TYPE;
    }
}
