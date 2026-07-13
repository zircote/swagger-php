<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter\Hierarchy\Spec;

use OpenApi\Spec as OA;

trait PlainTrait
{
    #[OA\Property(property: 'traitProp')]
    #[OA\Schema(type: 'string')]
    public string $traitProp;
}
