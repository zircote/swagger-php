<?php

namespace AnotherNamespace;

/**
 * @SWG\Definition()
 */
class Child extends \SwaggerFixtures\Ancestor
{

    /**
     * @var bool
     * @SWG\Property()
     */
    public $isBaby;
}
