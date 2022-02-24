<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ExpandClasses;

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
