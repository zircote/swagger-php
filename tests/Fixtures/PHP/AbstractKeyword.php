<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Attributes as OAT;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
abstract class AbstractKeyword
{
    #[OAT\Property(property: 'stuff')]
    abstract public function stuff(string $name, array $numbers): bool;

    #[OAT\Property(property: 'other')]
    public function other(): string
    {
        return 'other';
    }

    #[OAT\Property(property: 'another')]
    abstract public function another(): void;
}
