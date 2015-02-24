<?php

namespace Petstore;

/**
 * @SWG\Definition(required={"id", "name"})
 */
class Pet {

    /**
     * @SWG\Property(type="integer", format="int64")
     */
    public $id;

    /**
     * @SWG\Property(type="string")
     */
    public $name;

    /**
     * @SWG\Property(type="string")
     */
    public $tag;

}
