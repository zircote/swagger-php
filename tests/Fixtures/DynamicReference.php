<?php
namespace OpenApiFixtures;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Using a dynamic reference", version="unittest")
 */
class DynamicReference
{

    /**
     * @OA\Post(
     *     path="/api/path",
     *     summary="Post to URL",
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *              schema="body",
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  maximum=64
     *              ),
     *              @OA\Property(
     *                  property="description",
     *                  type="string"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Example extended response",
     *          ref="$/components/responses/Json",
     *          @OA\JsonContent(
     *              ref="$/components/schemas/Product",
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/components/schemas/Product"
     *              ),
     *              @OA\Property(
     *                  property="test",
     *                  ref="$/components/schemas/TestProperty"
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
 * @OA\Schema(
 *   schema="Product",
 *   @OA\Property(
 *      property="status",
 *      type="string",
 *      description="The status of a product",
 *      enum={"available", "discontinued"},
 *      default="available"
 *   )
 * )
 */

/**
 * @OA\Schema(
 *   schema="TestProperty",
 *   type="string",
 *   description="The status of a product",
 *   enum={"available", "discontinued"},
 *   default="available"
 * )
 */

/**
 * @OA\Response(
 *      response="Json",
 *      description="the basic response",
 *      @OA\JsonContent(
 *          @OA\Property(
 *              type="boolean",
 *              property="success"
 *          ),
 *          @OA\Property(
 *              property="data"
 *          ),
 *          @OA\Property(
 *              property="errors",
 *              type="object"
 *          ),
 *          @OA\Property(
 *              property="token",
 *              type="string"
 *          )
 *      )
 * )
 *
 */
