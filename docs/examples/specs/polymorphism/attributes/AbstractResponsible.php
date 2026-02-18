<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Polymorphism\Attributes;

use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'Responsible', discriminator: new OAT\Discriminator(
    propertyName: 'type',
    mapping: [
        'fl' => Fl::class,
        'employee' => Employee::class,
    ]
), oneOf: [
    new OAT\Schema(ref: Fl::class),
    new OAT\Schema(ref: Employee::class),
])]
abstract class AbstractResponsible
{
    protected const TYPE = null;

    #[OAT\Property(nullable: false, enum: ['employee', 'assignee', 'fl'])]
    protected string $type;

    public function __construct()
    {
        $this->type = static::TYPE;
    }
}
