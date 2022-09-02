<?php

namespace OpenApi\Examples\UsingInterfaces;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(title="Product model")
 */
class Product implements ProductInterface
{
    /**
     * The unique identifier of a product in our catalog.
     *
     * @var int
     *
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
}
