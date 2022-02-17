<?php

namespace OpenApi\Examples\Polymorphism;

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
