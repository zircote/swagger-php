<?php

namespace SwaggerTests\Fixtures\Shop\Model;

use Swagger\Annotations as SWG;

/**
 * @SWG\Definition
 */
class Product
{
    use CategoryTrait;

    /**
     * @var string
     * @SWG\Property
     */
    public $name;
}
