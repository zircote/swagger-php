<?php
namespace SwaggerTests\Fixtures\Models;

/**
 * @package
 * @category
 * @subpackage
 */
use Swagger\Annotations\Property;
use Swagger\Annotations\AllowableValues;
use Swagger\Annotations\Model;
use Swagger\Annotations\Items;

/**
 * @package
 * @category
 * @subpackage
 *
 * @Model(id="Pet")
 */
class Pet
{
    /**
     * @var array<Tags>
     *
     * @Property(type="array", items="$ref:Tag")
     */
    protected $tags = array();

    /**
     * @var int
     *
     * @Property(type="long")
     */
    protected $id;

    /**
     * @var Category
     *
     * @Property(type="Category")
     */
    protected $category;

    /**
     *
     *
     * @var string
     *
     * @Property(
     *      type="string",
     *      @allowableValues(
     *          valueType="LIST",
     *          values="['available', 'pending', 'sold']"
     *      ),
     *      description="pet status in the store")
     */
    protected $status;

    /**
     * @var string
     *
     * @Property(type="string")
     */
    protected $name;

    /**
     * @var array<string>
     *
     * @Property(type="array", @items(type="string"))
     */
    protected $photoUrls = array();
}

