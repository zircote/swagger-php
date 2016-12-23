<?php

namespace AnotherNamespace;

/**
 * @SWG\Definition()
 */
class ChildWithDocBlocks extends \SwaggerFixtures\AncestorWithoutDocBlocks
{

    /**
     * @var bool
     * @SWG\Property()
     */
    public $isBaby;
}
