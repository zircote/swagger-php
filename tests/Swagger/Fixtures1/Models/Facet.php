<?php
namespace SwaggerTests\Fixtures1\Models;

/**
 */
use Swagger\Annotations as SWG;

/**
 * @SWG\Model(id="Facet")
 */
class Facet
{
    /**
     * @var string
     * @SWG\Property(name="prop1", type="string")
     */
    public $prop1;
}
