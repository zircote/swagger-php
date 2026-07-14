<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter\Hierarchy\Spec;

use OpenApi\Spec as OA;

#[OA\Schema]
class ClassUsingPlainTrait
{
    use PlainTrait;

    #[OA\Property(property: 'ownProp')]
    #[OA\Schema(type: 'integer')]
    public int $ownProp;
}
