<?php

namespace PetstoreIO;

/**
 * @OAS\Schema(type="object", @OAS\Xml(name="Order"))
 */
class Order
{

    /**
     * @OAS\Property(format="int64")
     * @var int
     */
    public $id;

    /**
     * @OAS\Property(format="int64")
     * @var int
     */
    public $petId;

    /**
     * @OAS\Property(default=false)
     * @var bool
     */
    public $complete;

    /**
     * @OAS\Property(format="int32")
     * @var int
     */
    public $quantity;

    /**
     * @var \DateTime
     * @OAS\Property()
     */
    public $shipDate;

    /**
     * Order Status
     * @var string
     * @OAS\Property(enum={"placed", "approved", "delivered"})
     */
    public $status;
}
