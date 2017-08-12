<?php declare(strict_types=1);

namespace AnotherNamespace;

/**
 * @SWG\Schema()
 */
class ChildWithDocBlocks extends \SwaggerFixtures\AncestorWithoutDocBlocks
{

    /**
     * @var bool
     * @SWG\Property()
     */
    public $isBaby;
}
