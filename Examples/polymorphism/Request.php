<?php

namespace OpenApi\Examples\Polymorphism;

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
