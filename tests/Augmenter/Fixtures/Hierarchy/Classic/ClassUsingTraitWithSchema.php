<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter\Fixtures\Hierarchy\Classic;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class ClassUsingTraitWithSchema
{
    use TraitWithSchema;

    #[OAT\Property(type: 'integer')]
    public int $age;
}
