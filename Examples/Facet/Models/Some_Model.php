<?php
namespace Examples;
use Swagger\Annotations as SWG;
/**
 * @SWG\Model(id="Examples\Some_Model")
 */
class Some_Model
{
    /**
     * @SWG\Property()
     * @var int
     */
    public $id; 
    
    /**
     * @SWG\Property(items="$ref:Examples\Some_Model")
     * @var array
     */
    public $children;
}
