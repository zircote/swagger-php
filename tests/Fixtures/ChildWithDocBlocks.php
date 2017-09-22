<?php declare(strict_types=1);

namespace AnotherNamespace;

/**
 * @OAS\Schema()
 */
class ChildWithDocBlocks extends \SwaggerFixtures\AncestorWithoutDocBlocks
{

    /**
     * @var bool
     * @OAS\Property()
     */
    public $isBaby;
}
