<?php

namespace OpenApi\Examples\PetstoreSwaggerIo\Models;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     @OA\Xml(name="Category")
 * )
 */
class Category
{
    /**
     * @OA\Property(format="int64")
     *
     * @var int
     */
    public $id;

    /**
     * @OA\Property
     *
     * @var string
     */
    public $name;
}
