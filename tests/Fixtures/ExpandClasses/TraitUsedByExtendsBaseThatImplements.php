<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ExpandClasses;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
trait TraitUsedByExtendsBaseThatImplements
{
    /**
     * @var string
     */
    #[OAT\Property(property: 'traitProperty')]
    public function getTraitProperty()
    {
        return 'bar';
    }
}
