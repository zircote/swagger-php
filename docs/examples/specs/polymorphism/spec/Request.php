<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Polymorphism\Spec;

use OpenApi\Spec as OA;

#[OA\Schema]
final class Request
{
    protected const TYPE = 'employee';

    #[OA\Property(property: 'payload')]
    public AbstractResponsible $payload;
}
