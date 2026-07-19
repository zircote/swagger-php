<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Polymorphism\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'FlResponsible')]
final class Fl extends AbstractResponsible
{
    public const TYPE = 'fl';

    #[OA\Property(property: 'property3')]
    public ?string $property3 = null;
}
