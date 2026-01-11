<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ExpandClasses;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class ExtendsBaseThatImplements extends BaseThatImplements
{
    use TraitUsedByExtendsBaseThatImplements;

    /**
     * @var string
     */
    #[OAT\Property]
    public $extendsProperty;
}
