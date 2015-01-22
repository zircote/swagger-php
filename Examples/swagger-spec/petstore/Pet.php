<?php

namespace Petstore;

/**
 * @SWG\Definition(name="Pet", required={"id", "name"})
 */
class Pet {

    /**
     * @SWG\Property(name="id", type="integer", format="int64")
     */
    public $id;

    /**
     * @SWG\Property(name="name", type="string")
     */
    public $name;

    /**
     * @SWG\Property(name="tag", type="string")
     */
    public $tag;

}
