<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingInterfaces\Spec;

use OpenApi\Spec as OA;

/**
 * Not a schema.
 */
interface ColorInterface
{
    /**
     * The product color.
     */
    #[OA\Property(property: 'color')]
    #[OA\Schema(example: 'blue')]
    public function getColor();
}
