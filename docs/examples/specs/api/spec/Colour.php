<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Spec;

use OpenApi\Spec as OA;

/**
 * A Colour.
 */
#[OA\Schema(schema: 'Colour')]
enum Colour
{
    case GREEN;
    case BLUE;
    case RED;
}
