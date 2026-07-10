<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter\Fixtures\Hierarchy\Spec;

use OpenApi\Spec as OA;

#[OA\Schema]
class ParentWithSchema
{
    #[OA\Property(property: 'baseProp')]
    #[OA\Schema(type: 'string')]
    public string $baseProp;
}
