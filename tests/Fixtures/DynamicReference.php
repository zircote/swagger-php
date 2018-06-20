<?php
namespace SwaggerFixtures;

use Swagger\Annotations as OAS;

/**
 * @OAS\Info(title="Using a dynamic reference", version="unittest")
 */
class DynamicReference
{

    /**
     * @OAS\Post(
     *     path="/api/path",
     *     summary="Post to URL",
     *     @OAS\MediaType(
     *         mediaType="multipart/form-data",
     *         @OAS\Schema(
     *              schema="body",
     *              @OAS\Property(
     *                  property="name",
     *                  type="string",
     *                  maximum=64
     *              ),
     *              @OAS\Property(
     *                  property="description",
     *                  type="string"
     *              )
     *          )
     *     ),
     *     @OAS\Response(
     *          response=200,
     *          description="Example extended response",
     *          ref="$/components/responses/Json",
     *          @OAS\MediaType(
     *              mediaType="application/json",
     *              @OAS\Schema(
     *                  ref="$/components/schemas/Product",
     *                  @OAS\Property(
     *                      property="data",
     *                      ref="#/components/schemas/Product"
     *                  ),
     *                  @OAS\Property(
     *                      property="test",
     *                      ref="$/components/schemas/TestProperty"
     *                  )
     *              )
     *          ),
     *     ),
     *     security={{"Bearer":{}}}
     * )
     */
    public function postSomething()
    {
    }
}

/**
 * @OAS\Schema(
 *   schema="Product",
 *   @OAS\Property(
 *      property="status",
 *      type="string",
 *      description="The status of a product",
 *      enum={"available", "discontinued"},
 *      default="available"
 *   )
 * )
 */

/**
 * @OAS\Schema(
 *   schema="TestProperty",
 *   type="string",
 *   description="The status of a product",
 *   enum={"available", "discontinued"},
 *   default="available"
 * )
 */

/**
 * @OAS\Response(
 *      response="Json",
 *      description="the basic response",
 *      @OAS\MediaType(
 *          mediaType="application/json",
 *          @OAS\Schema(
 *              @OAS\Property(
 *                  type="boolean",
 *                  property="success"
 *              ),
 *              @OAS\Property(
 *                  property="data"
 *              ),
 *              @OAS\Property(
 *                  property="errors",
 *                  type="object"
 *              ),
 *              @OAS\Property(
 *                  property="token",
 *                  type="string"
 *              )
 *          )
 *      )
 * )
 *
 */
