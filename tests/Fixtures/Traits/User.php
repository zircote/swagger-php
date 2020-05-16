<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures\Traits;

use OpenApiTests\Fixtures\Traits\HelloTrait as Hello;
use OpenApiTests\Fixtures\Traits\Sub\SubClass as ParentClass;

class User extends ParentClass implements \OpenApiTests\Fixtures\Traits\UserInterface
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
