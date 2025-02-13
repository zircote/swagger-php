<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Annotations;

use OpenApi\Annotations as OA;

/**
 * A Colour.
 *
 * @OA\Schema()
 */
enum Colour
{
    case GREEN;
    case BLUE;
    case RED;
}
