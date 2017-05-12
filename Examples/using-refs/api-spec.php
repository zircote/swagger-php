<?php

/**
 * @SWG\Info(
 *   version="1.0.0",
 *   title="Example of using references in swagger-php",
 * )
 */
?>
You can define top-level parameters which can be references with $ref="#/parameters/$parameter"
<?php
/**
 * @SWG\Parameter(
 *   parameter="product_id_in_path_required",
 *   name="product_id",
 *   description="The ID of the product",
 *   type="integer",
 *   format="int64",
 *   in="path",
 *   required=true
 * )
 *
 * @SWG\Parameter(
 *   parameter="product_in_body",
 *   in="body",
 *   name="product",
 *   @SWG\Schema(ref="#/definitions/Product")
 * )
 */
?>
You can define top-level responses which can be references with $ref="#/responses/$response"

I find it usefull to add @SWG\Response(ref="#/responses/todo") to the operations when i'm starting out with writting the swagger documentation.
As it bypasses the "@SWG\Get() requires at least one @SWG\Response()" error and you'll get a nice list of the available api calls in swagger-ui.

Then later, a search for '#/responses/todo' will reveal the operations I haven't documented yet.
<?php
/**
 * @SWG\Response(
 *   response="product",
 *   description="All information about a product",
 *   @SWG\Schema(ref="#/definitions/Product")
 * )
 *
 * @SWG\Response(
 *   response="todo",
 *   description="This API call has no documentated response (yet)",
 * )
 */
?>

And although definitions are generally used for model-level schema's' they can be used for smaller things as well.
Like a @SWG\Schema, @SWG\Property or @SWG\Items that is uses multiple times.

<?php
/**
 * @SWG\Definition(
 *   definition="product_status",
 *   type="string",
 *   description="The status of a product",
 *   enum={"available", "discontinued"},
 *   default="available"
 * )
 */
