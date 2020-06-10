<?php

namespace OpenApiTests\Fixtures\InheritProperties;

/**
 * @OA\Schema()
 */
class ExtendsBaseThatImplements extends BaseThatImplements
{
    use TraitUsedByExtendsBaseThatImplements;

    /**
     * @OA\Property();
     * @var string
     */
    public $extendsProperty;
}
