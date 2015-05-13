<?php

namespace SwaggerTests\Fixtures\Shop\Model;

use Swagger\Annotations as SWG;

/**
 * @SWG\Definition(definition="Product")
 */
class Product
{
    use CategoryTrait;

    /**
     * @var string
     * @SWG\Property(property="name", type="string")
     */
    public $name;
}
