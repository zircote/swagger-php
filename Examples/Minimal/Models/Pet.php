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
     * @var Tag[]
     *
     * @SWG\Property()
     */
    public $tags = array();
    // Autodetected:
    // @SWG\Property->name is set to "tags" based on property name $tags.
    // @SWG\Property->type is detected as "array" based on @var ending with "[]"
    // @SWG\Property->items is detected as @SWG\Items("$ref:Tag") based on @var Tag[]

    /**
     * @var int
     *
     * @SWG\Property()
	 * Property() will use the property name "id" as name and detect the "@var int" and use "integer" as type.
     */
    public $id;

    /**
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

