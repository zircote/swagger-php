<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter\Hierarchy\Classic;

use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'NameTrait')]
trait TraitWithSchema
{
    #[OAT\Property(description: 'The name.')]
    public string $name;
}
