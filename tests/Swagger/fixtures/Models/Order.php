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

/**
 * @package
 * @category
 * @subpackage
 *
 * @Model(id="Order")
 */
class Order
{
    /**
     * @var int
     * @Property(type="long")
     */
    protected $id;

    /**
     * @var int
     * @Property(type="long")
     */
    protected $petId;

    /**
     * @var string
     * @Property(type="string",
     *      @allowableValues(valueType="LIST",
     *          values="['placed','approved','delivered']"
     *      ),
     *      description="Order Status"
     *  )
     */
    protected $status;

    /**
     * @var int
     * @Property(type="int")
     */
    protected $quantity;

    /**
     * @var string
     * @Property(type="Date")
     */
    protected $shipDate;
}

