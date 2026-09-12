<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'DeclaredRequiredMerged', required: ['declared'])]
class DeclaredRequiredMerged
{
    #[OAT\Property]
    public string $declared;

    #[OAT\Property(required: true)]
    public string $flagged;
}
