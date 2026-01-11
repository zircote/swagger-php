<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Parser;

use OpenApi\Attributes as OAT;

interface UserInterface extends OtherInterface
{
    /**
     * The first name of the user.
     *
     * @return string
     *@example John
     */
    #[OAT\Property]
    public function getFirstName();
}
