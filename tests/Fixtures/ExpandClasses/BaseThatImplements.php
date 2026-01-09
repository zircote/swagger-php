<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ExpandClasses;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class BaseThatImplements implements BaseInterface
{
    /**
     * @var string
     */
    #[OAT\Property]
    public $baseProperty;

    /**
     * @inheritDoc
     */
    public function getInterfaceProperty()
    {
        return 'foo';
    }
}
