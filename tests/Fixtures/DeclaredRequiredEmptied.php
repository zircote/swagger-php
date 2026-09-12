<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'DeclaredRequiredEmptied', required: ['only'])]
class DeclaredRequiredEmptied
{
    #[OAT\Property(required: false)]
    public string $only;
}
