<?php
namespace SwaggerFixtures;

/**
 * @SWG\Info(title="Using a dynamic reference", version="unittest")
 */
class DynamicReference
{

    /**
     * @SWG\Post(
     *     path="/api/path",
     *     summary="Post to URL",
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="name",
     *                  type="string",
     *                  maximum=64
     *              ),
     *              @SWG\Property(
     *                  property="description",
     *                  type="string"
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Example extended response",
     *          ref="$/responses/Json",
     *          @SWG\Schema(
     *              ref="$/definitions/Product",
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Product"
     *              ),
     *              @SWG\Property(
     *                  property="test",
     *                  ref="$/definitions/TestProperty"
     *              )
     *          )
     *     ),
     *     security={{"Bearer":{}}}
     * )
     */
    public function postSomething()
    {
    }
}

/**
 * @SWG\Definition(
 *   definition="Product",
 *   @SWG\Property(
 *      property="status",
 *      type="string",
 *      description="The status of a product",
 *      enum={"available", "discontinued"},
 *      default="available"
 *   )
 * )
 */

/**
 * @SWG\Definition(
 *   definition="TestProperty",
 *   type="string",
 *   description="The status of a product",
 *   enum={"available", "discontinued"},
 *   default="available"
 * )
 */

/**
 * @SWG\Response(
 *      response="Json",
 *      description="the basic response",
 *      @SWG\Schema(
 *          @SWG\Property(
 *              type="boolean",
 *              property="success"
 *          ),
 *          @SWG\Property(
 *              property="data"
 *          ),
 *          @SWG\Property(
 *              property="errors",
 *              type="object"
 *          ),
 *          @SWG\Property(
 *              property="token",
 *              type="string"
 *          )
 *      )
 * )
 *
 */
