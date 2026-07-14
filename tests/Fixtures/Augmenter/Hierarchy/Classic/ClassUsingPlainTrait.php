<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter\Hierarchy\Classic;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class ClassUsingPlainTrait
{
    use PlainTrait;

    #[OAT\Property(type: 'integer')]
    public int $ownProp;
}
