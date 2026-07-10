<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter\Fixtures\Hierarchy\Classic;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class ParentWithSchema
{
    #[OAT\Property(type: 'string')]
    public string $baseProp;
}
