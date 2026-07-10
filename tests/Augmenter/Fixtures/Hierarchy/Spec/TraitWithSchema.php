<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter\Fixtures\Hierarchy\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'NameTrait')]
trait TraitWithSchema
{
    #[OA\Property(property: 'name')]
    #[OA\Schema(description: 'The name.')]
    public string $name;
}
