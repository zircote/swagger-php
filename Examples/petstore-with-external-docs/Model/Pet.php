<?php

namespace PetstoreWithExternalDocs\Model;

use Swagger\Annotations as SWG;

/**
 * @SWG\Definition(required={"name"}, type="object", @SWG\Xml(name="Pet"))
 */
class Pet
{
    /**
     * @SWG\Property(example="Brutus")
     * @var string
     */
    public $name;
}
