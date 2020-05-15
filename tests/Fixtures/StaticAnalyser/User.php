<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures\StaticAnalyser;

use OpenApiTests\Fixtures\HelloTrait as Hello;
use OpenApiTests\Fixtures\StaticAnalyser\Sub\SubClass as ParentClass;

class User extends ParentClass implements \OpenApiTests\Fixtures\StaticAnalyser\UserInterface
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
