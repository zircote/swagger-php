<?php

namespace OpenApi\Examples\PetstoreSwaggerIo\Models;

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
