<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter\Hierarchy\Classic;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class ClassUsingOrderedTraits extends PlainParent
{
    use PlainTrait;
    use OrderedTrait;

    #[OAT\Property(type: 'integer')]
    public int $ownProp;
}
