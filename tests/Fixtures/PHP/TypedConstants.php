<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

/**
 * Typed class constants and dynamic class constant fetch (8.3), plus `final const` (8.1).
 */
class TypedConstants
{
    public const string NAME = 'name';

    public const int COUNT = 1;

    final public const array TAGS = ['a', 'b'];

    protected const FirstInterface|null CONTRACT = null;

    public function fetch(string $which): mixed
    {
        return self::{$which};
    }
}
