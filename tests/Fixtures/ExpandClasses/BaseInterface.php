<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ExpandClasses;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
interface BaseInterface
{
    /**
     * @var string
     */
    #[OAT\Property(property: 'interfaceProperty')]
    public function getInterfaceProperty();
}
