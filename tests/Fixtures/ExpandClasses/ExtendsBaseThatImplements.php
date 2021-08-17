<?php

namespace OpenApi\Tests\Fixtures\ExpandClasses;

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
