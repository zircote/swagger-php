<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter\Hierarchy\Spec;

use OpenApi\Spec as OA;

#[OA\Schema]
class ClassUsingOrderedTraits extends PlainParent
{
    use PlainTrait;
    use OrderedTrait;

    #[OA\Property(property: 'ownProp')]
    #[OA\Schema(type: 'integer')]
    public int $ownProp;
}
