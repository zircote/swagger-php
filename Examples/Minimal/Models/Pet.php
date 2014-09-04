<?php
namespace Minimal\Models;

// swagger-php uses the @SWG namespace by default. The `use Swagger\Annotations as SWG;` statement is optional.


/**
 * @SWG\Model()
 * Model() will use the classname "Pet" as id
 */
class Pet
{
    /**
     * Unique identifier for the pet
     * @var int
     *
     * @SWG\Property()
     */
    public $id;
    // Autodetected:
    // @Property->name is set to "id" based on the propertyname $id
    // @Property-type is set to "integer" based on the "@var int"
    // @Property->description is set to "Unique identifier for the Pet" extracted from the docblock

    /**
     * Tags assigned to this pet
     * @var Tag[]
     *
     * @SWG\Property()
     */
    public $tags = array();
    // Autodetected:
    // @Property->name is set to "tags" based on property name $tags.
    // @Property->type is detected as "array" based on @var ending with "[]"
    // @Property->items is detected as @Items("$ref:Tag") based on @var Tag[]

    /**
     * Category the pet is in
     * @var Category
     *
     * @SWG\Property()
     */
    public $category;

    /**
     * Pet status in the store
     *
     * @var string
     *
     * @SWG\Property(enum="['available', 'pending', 'sold']")
     * Property() will use the part of the docblock above the first @ as description.
     */
    public $status;

    /**
     * Friendly name of the pet
     * @var string
     *
     * @SWG\Property()
     */
    public $name;

    /**
     * @var string[]
     *
     * @SWG\Property()
     */
    public $photoUrls = array();
}

