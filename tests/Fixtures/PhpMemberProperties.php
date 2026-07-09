<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class PhpMemberProperties
{
    public function __construct(
        #[OAT\Property]
        public string $promoted,
        #[OAT\Property]
        public ?string $promotedNullable,
    ) {
    }

    #[OAT\Property(property: 'computed')]
    public function computed(): int
    {
        return 0;
    }
}
