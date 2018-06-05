<?php

/**
 * @OAS\Info(
 *   version="1.0.0",
 *   title="Example of using references in swagger-php",
 * )
 */
?>
You can define top-level parameters which can be references with $ref="#/parameters/$parameter"
<?php
/**
 * @OAS\Parameter(
 *   parameter="product_id_in_path_required",
 *   name="product_id",
 *   description="The ID of the product",
 *   @OAS\Schema(
 *     type="integer",
 *     format="int64",
 *   ),
 *   in="path",
 *   required=true
 * )
 *
 * @OAS\RequestBody(
 *   request="product_in_body",
 *   required=true,
 *   description="product_request",
 *   @OAS\MediaType(
 *     mediaType="application/json",
 *     @OAS\Schema(ref="#/components/schemas/Product")
 *   )
 * )
 */
?>
You can define top-level responses which can be references with $ref="#/responses/$response"

I find it usefull to add @OAS\Response(ref="#/responses/todo") to the operations when i'm starting out with writting the swagger documentation.
As it bypasses the "@OAS\Get() requires at least one @OAS\Response()" error and you'll get a nice list of the available api calls in swagger-ui.

Then later, a search for '#/responses/todo' will reveal the operations I haven't documented yet.
<?php
/**
 * @OAS\Response(
 *   response="product",
 *   description="All information about a product",
 *   @OAS\MediaType(
 *     mediaType="application/json",
 *     @OAS\Schema(ref="#/components/schemas/Product")
 *   )
 * )
 *
 * @OAS\Response(
 *   response="todo",
 *   description="This API call has no documentated response (yet)",
 * )
 */
?>

And although definitions are generally used for model-level schema's' they can be used for smaller things as well.
Like a @OAS\Schema, @OAS\Property or @OAS\Items that is uses multiple times.

<?php
/**
 * @OAS\Schema(
 *   schema="product_status",
 *   type="string",
 *   description="The status of a product",
 *   enum={"available", "discontinued"},
 *   default="available"
 * )
 */
