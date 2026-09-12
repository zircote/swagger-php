<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter\Hierarchy\Spec;

use OpenApi\Spec as OA;

trait OrderedTrait
{
    #[OA\Property(property: 'orderedProp')]
    #[OA\Schema(type: 'string')]
    public string $orderedProp;
}
