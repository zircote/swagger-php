<?php

namespace Petstore;

/**
 * @SWG\Definition(name="pet", required={"id", "name"})
 */
class SimplePet {

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
