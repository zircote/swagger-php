<?php

namespace PetstoreIO;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="Category")
 * )
 */
class Category
{

    /**
     * @SWG\Property(format="int64")
     * @var int
     */
    public $id;

    /**
     * @SWG\Property()
     * @var string
     */
    public $name;
}
