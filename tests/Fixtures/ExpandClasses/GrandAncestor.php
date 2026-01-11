<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ExpandClasses;

use OpenApi\Attributes as OAT;

class GrandAncestor
{
    /**
     * @var string
     */
    #[OAT\Property]
    public $firstname;

    /**
     * @var string
     */
    #[OAT\Property(property: 'lastname')]
    public $lastname;
}
