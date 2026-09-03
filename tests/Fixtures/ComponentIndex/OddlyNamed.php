<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ComponentIndex;

use OpenApi\Spec as OA;

/**
 * A component name is free-form, so both characters that are structural in a JSON Pointer
 * are legal in one.
 */
#[OA\Schema(schema: 'Odd/Name~With')]
class OddlyNamed
{
    #[OA\Property]
    public string $name;
}
