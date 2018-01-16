<?php declare(strict_types=1);

namespace AnotherNamespace;

/**
 * @OAS\Schema()
 */
class Child extends \SwaggerFixtures\Ancestor
{

    /**
     * @var bool
     * @OAS\Property()
     */
    public $isBaby;
}
