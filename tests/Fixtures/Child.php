<?php declare(strict_types=1);

namespace AnotherNamespace;

/**
 * @SWG\Schema()
 */
class Child extends \SwaggerFixtures\Ancestor
{

    /**
     * @var bool
     * @SWG\Property()
     */
    public $isBaby;
}
