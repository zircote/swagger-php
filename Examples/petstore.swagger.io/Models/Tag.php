<?php

namespace OpenApi\Examples\PetstoreSwaggerIo\Models;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     @OA\Xml(name="Tag")
 * )
 */
class Tag
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
