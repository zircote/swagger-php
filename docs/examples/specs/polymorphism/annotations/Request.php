<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Polymorphism\Annotations;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="Request")
 */
final class Request
{
    protected const TYPE = 'employee';

    /**
     * @OA\Property(nullable=false)
     *
     * @var AbstractResponsible
     */
    public $payload;
}
