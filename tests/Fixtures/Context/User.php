<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures\Context;

use OpenApiTests\Fixtures\HelloTrait as Hello;
use OpenApiTests\Fixtures\Sub\SubClass as ParentClass;

class User extends ParentClass implements \OpenApiTests\Fixtures\UserInterface
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
