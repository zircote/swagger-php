<?php

namespace petstore;

/**
 * @OAS\Schema(required={"id", "name"})
 */
class Pet
{

    /**
     * @OAS\Property(type="integer", format="int64")
     */
    public $id;

    /**
     * @OAS\Property()
     * @var string
     */
    public $name;

    /**
     * @OAS\Property()
     * @var string
     */
    public $tag;
}
