<?php
namespace Minimal\Models;

// swagger-php uses the @SWG namespace by default. The `use Swagger\Annotations as SWG;` statement is optional.

/**
 * @SWG\Model()
 * Model() will use the classname "Dog" as id and inherit all swagger properties from Pet
 */
abstract class Dog extends Pet
{
    /**
     * @SWG\Property(required=true)
     * @var string
     */
    public $breed;

    /**
     * @var Dog
     *
     * @SWG\Property()
     */
    protected $parent;
}