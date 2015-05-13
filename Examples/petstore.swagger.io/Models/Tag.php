<?php

namespace PetstoreIO\Models;

use Swagger\Annotations as SWG;

/**
 * @SWG\Definition(
 *   @SWG\Xml(name="Tag")
 * )
 */
class Tag
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
