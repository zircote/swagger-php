<?php

namespace OpenApiTests\Fixtures\InheritProperties;

/**
 * @OA\Schema()
 */
trait TraitUsedByExtendsBaseThatImplements
{

    /**
     * @OA\Property(property="traitProperty");
     * @var string
     */
    public function getTraitProperty()
    {
        return "bar";
    }
}
