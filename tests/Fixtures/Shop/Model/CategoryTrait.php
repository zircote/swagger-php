<?php

namespace SwaggerTests\Fixtures\Shop\Model;

use Swagger\Annotations as SWG;

trait CategoryTrait
{
    /**
     * @var string
     * @SWG\Property(property="category", type="string")
     */
    public $category;
}
