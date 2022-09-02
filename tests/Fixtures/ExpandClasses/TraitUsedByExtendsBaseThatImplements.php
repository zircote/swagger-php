<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ExpandClasses;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema
 */
trait TraitUsedByExtendsBaseThatImplements
{
    /**
     * @OA\Property(property="traitProperty");
     *
     * @var string
     */
    public function getTraitProperty()
    {
        return 'bar';
    }
}
