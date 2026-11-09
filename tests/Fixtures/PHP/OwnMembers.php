<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Tests\Fixtures\PHP\Inheritance\BaseClass;

trait SharedMembers
{
    public const SHARED = 'shared';

    public string $shared = '';

    public function shared(): void
    {
    }
}

/**
 * Members of its own, members a trait brings in, and members inherited from a parent.
 */
class OwnMembers extends BaseClass
{
    use SharedMembers;

    public const OWN = 'own';

    public string $own = '';

    public function own(): void
    {
    }
}
