<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures\StaticAnalyser;

interface UserInterface extends OtherInterface
{

    /**
     * The first name of the user.
     *
     * @return string
     * @example John
     */
    public function getFirstName();
}
