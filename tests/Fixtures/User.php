<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures;

class User implements UserInterface
{
    use \OpenApiTests\Fixtures\HelloTrait;

    /**
     * {@inheritDoc}
     */
    public function getFirstName()
    {
        return 'Joe';
    }
}
