<?php

namespace OpenApi\Examples\UsingInterfaces;

/**
 * @OA\Schema(title="Product model")
 */
class Product implements ProductInterface, ColorInterface
{

    /**
     * The unique identifier of a product in our catalog.
     *
     * @var int
     * @OA\Property(format="int64", example=1)
     */
    public $id;

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'kettle';
    }

    /**
     * @inheritDoc
     */
    public function getColor()
    {
        return 'green';
    }
}
