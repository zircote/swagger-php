<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

/**
 * A property whose docblock type cannot be read, for the warning it gives.
 */
#[OA\Schema]
class UnreadableDocblockType
{
    /** @var NoSuchClass */
    #[OA\Property]
    public $unknown;
}
