<?php
namespace Facet\Models;

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
    /**
     * @var string some desc
     * @SWG\Property
     */
    public $prop2;
    /**
     * @var Some_Object
     * @SWG\Property
     */
    public $prop3;
}
