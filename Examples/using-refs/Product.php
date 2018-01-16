<?php
namespace UsingRefs;
?>
A common scenario is to let swagger-php generate a definition based on your model class.
These definitions can then be referenced with `ref="#/components/schemas/$classname"
<?php
/**
 * @OAS\Schema(
 *     description="Product model",
 *     type="object",
 *     title="Product model"
 * )
 */
class Product {

    /**
     * The unique identifier of a product in our catalog.
     *
     * @var integer
     * @OAS\Property(format="int64", example=1)
     */
    public $id;

    /**
     * @OAS\Property(ref="#/components/schemas/product_status")
     */
    public $status;
}
