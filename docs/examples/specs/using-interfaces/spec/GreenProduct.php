<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingInterfaces\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(title: 'GreenProduct')]
class GreenProduct extends Product implements ColorInterface
{
    public function getColor(): string
    {
        return 'green';
    }
}
