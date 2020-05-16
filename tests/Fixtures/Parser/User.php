<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures\Parser;

use OpenApiTests\Fixtures\Parser\HelloTrait as Hello;
use OpenApiTests\Fixtures\Parser\Sub\SubClass as ParentClass;

class User extends ParentClass implements \OpenApiTests\Fixtures\Parser\UserInterface
{
    use Hello;

    /**
     * {@inheritDoc}
     */
    public function getFirstName()
    {
        return 'Joe';
    }
}
