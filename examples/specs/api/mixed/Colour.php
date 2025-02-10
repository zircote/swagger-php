<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Mixed;

use OpenApi\Attributes as OAT;

/**
 * A Colour.
 */
#[OAT\Schema()]
enum Colour
{
    case GREEN;
    case BLUE;
    case RED;
}
