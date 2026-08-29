<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ComponentIndex;

use OpenApi\Spec as OA;

/**
 * Carries no explicit `schema` name, so before the `Names` augmenter runs it is reachable
 * by FQCN only.
 */
#[OA\Schema]
class UnnamedTag
{
    #[OA\Property]
    public string $label;
}
