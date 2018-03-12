<?php

/**
 * @license Apache 2.0
 */

namespace Petstore30\controllers;

/**
 * Class Store
 *
 * @package Petstore30\controllers
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class Store
{
    /**
     * @OAS\Get(
     *     path="/store",
     *     tags={"store"},
     *     summary="Returns pet inventories by status",
     *     description="Returns a map of status codes to quantities",
     *     operationId="getInventory",
     *     @OAS\Response(
     *         response=200,
     *          description="successful operation",
     *          @OAS\MediaType(
     *              mediaType="application/json",
     *              @OAS\Schema(
     *                  type="object",
     *                  @OAS\AdditionalProperties(
     *                      type="integer",
     *                      format="int32"
     *                  )
     *              )
     *          )
     *     ),
     *     security={
     *         {"api_key": {}}
     *     }
     * )
     */
    public function getInventory()
    {
    }

    /**
     * @OAS\Post(
     *     path="/store/order",
     *     tags={"store"},
     *     summary="Place an order for a pet",
     *     operationId="placeOrder",
     *     @OAS\Response(
     *         response=200,
     *         description="successful operation",
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(
     *                 ref="#/components/schemas/Order"
     *             )
     *         ),
     *         @OAS\MediaType(
     *             mediaType="application/xml",
     *             @OAS\Schema(
     *                 ref="#/components/schemas/Order"
     *             )
     *         )
     *     ),
     *     @OAS\RequestBody(
     *         description="order placed for purchasing th pet",
     *         required=true,
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(
     *                 ref="#/components/schemas/Order"
     *             )
     *         )
     *     )
     * )
     */
    public function placeOrder()
    {
    }

    /**
     * @OAS\Get(
     *     path="/store/order/{orderId}",
     *     tags={"store"},
     *     description=">-
    For valid response try integer IDs with value >= 1 and <= 10.\ \ Other
    values will generated exceptions",
     *     operationId="getOrderById",
     *     @OAS\Parameter(
     *         name="orderId",
     *         in="path",
     *         description="ID of pet that needs to be fetched",
     *         required=true,
     *         @OAS\Schema(
     *             type="integer",
     *             format="int64",
     *             maximum=1,
     *             minimum=10
     *         )
     *     ),
     *     @OAS\Response(
     *         response=200,
     *         description="successful operation",
     *         @OAS\MediaType(
     *             mediaType="application/json",
     *             @OAS\Schema(
     *                 ref="#/components/schemas/Order"
     *             )
     *         ),
     *         @OAS\MediaType(
     *             mediaType="application/xml",
     *             @OAS\Schema(
     *                 ref="#/components/schemas/Order"
     *             )
     *         )
     *     ),
     *     @OAS\Response(
     *         response=400,
     *         description="Invalid ID supplied"
     *     ),
     *     @OAS\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
    public function getOrderById()
    {
    }

    /**
     * @OAS\Delete(
     *     path="/store/order/{orderId}",
     *     tags={"store"},
     *     summary="Delete purchase order by ID",
     *     description=">-
    For valid response try integer IDs with positive integer value.\ \
    Negative or non-integer values will generate API errors",
     *     operationId="deleteOrder",
     *     @OAS\Parameter(
     *         name="orderId",
     *         in="path",
     *         required=true,
     *         description="ID of the order that needs to be deleted",
     *         @OAS\Schema(
     *             type="integer",
     *             format="int64",
     *             minimum=1
     *         )
     *     ),
     *     @OAS\Response(
     *         response=400,
     *         description="Invalid ID supplied"
     *     ),
     *     @OAS\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * ),
     */
    public function deleteOrder()
    {
    }
}
