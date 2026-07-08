<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Spec\Fixtures\Api;

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
