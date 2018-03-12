<?php

namespace PetstoreIO;

/**
 * @OAS\Schema(
 *   type="object",
 *   @OAS\Xml(name="Tag")
 * )
 */
class Tag
{

    /**
     * @OAS\Property(format="int64")
     * @var int
     */
    public $id;

    /**
     * @OAS\Property()
     * @var string
     */
    public $name;
}
