<?php

namespace OpenApi\Examples\Specs\Polymorphism\Annotations;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="FlResponsible")
 */
final class Fl extends AbstractResponsible
{
    public const TYPE = 'fl';

    /**
     * @OA\Property(nullable=true)
     *
     * @var string
     */
    public $property3;
}
