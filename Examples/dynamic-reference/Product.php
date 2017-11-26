<?php
namespace UsingRefs;
?>
A common scenario is to let swagger-php generate a definition based on your model class.
These definitions can then be referenced with `ref="#/definitions/$classname"
<?php
/**
 * @SWG\Definition()
 */
class Product {

    /**
     * The unique identifier of a product in our catalog.
     *
     * @var integer
     * @SWG\Property(format="int64")
     */
    public $id;

    /**
     * @var boolean
     * @SWG\Property(format="boolean")
     */
    public $status;
}
