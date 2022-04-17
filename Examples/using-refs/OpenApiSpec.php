<?php

namespace OpenApi\Examples\UsingRefs;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Example of using references in swagger-php",
 * )
 */
class OpenApiSpec
{
}

/**
 * You can define top-level parameters which can be references with $ref="#/components/parameters/parameter".
 *
 * @OA\Parameter(
 *     parameter="product_id_in_path_required",
 *     name="product_id",
 *     description="The ID of the product",
 *     @OA\Schema(
 *         type="integer",
 *         format="int64",
 *     ),
 *     in="path",
 *     required=true
 * )
 */
class ProductIdParamerter
{
}

/**
 * @OA\RequestBody(
 *     request="product_in_body",
 *     required=true,
 *     description="product_request",
 *     @OA\JsonContent(ref="#/components/schemas/Product")
 * )
 */
class ProductRequestBody
{
}

/**
 * You can define top-level responses which can be references with $ref="#/components/responses/response".
 *
 * I find it useful to add `@OA\Response(ref="#/components/responses/todo")` to the operations when i'm starting out with
 * writing the swagger documentation.
 * As it bypasses the `@OA\Get()` it requires at least one `@OA\Response()` error and you'll get
 * a nice list of the available api calls in swagger-ui.
 *
 * Then later, a search for '#/components/responses/todo' will reveal the operations I haven't documented yet.
 *
 * @OA\Response(
 *     response="product",
 *     description="All information about a product",
 *     @OA\JsonContent(ref="#/components/schemas/Product")
 * )
 */
class ProductResponse
{
}

/**
 * @OA\Response(
 *     response="todo",
 *     description="This API call has no documentated response (yet)",
 * )
 */
class TodoResponse
{
}

/**
 * And although definitions are generally used for model-level schema's' they can be used for smaller things as well.
 *
 * Like a `@OA\Schema`, `@OA\Property` or `@OA\Items` that is uses multiple times.
 *
 * @OA\Schema(
 *     schema="product_status",
 *     type="string",
 *     description="The status of a product",
 *     enum={"available", "discontinued"},
 *     default="available"
 * )
 */
class ProductStatus
{
}
