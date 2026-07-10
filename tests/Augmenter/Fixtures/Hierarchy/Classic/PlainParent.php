<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter\Fixtures\Hierarchy\Classic;

use OpenApi\Attributes as OAT;

class PlainParent
{
    #[OAT\Property(type: 'string')]
    public string $parentProp;
}
