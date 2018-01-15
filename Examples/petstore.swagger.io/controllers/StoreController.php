<?php

namespace PetstoreIO;

abstract class StoreController
{

    /**
     * @OAS\Get(path="/store/inventory",
     *   tags={"store"},
     *   summary="Returns pet inventories by status",
     *   description="Returns a map of status codes to quantities",
     *   operationId="getInventory",
     *   parameters={},
     *   @OAS\Response(
     *     response=200,
     *     description="successful operation",
     *     @OAS\Schema(
     *       type="object",
     *       additionalProperties={
     *         "type":"integer",
     *         "format":"int32"
     *       }
     *     )
     *   ),
     *   security={{
     *     "api_key":{}
     *   }}
     * )
     */
    public function getInventory()
    {
    }

    /**
     * @OAS\Post(path="/store/order",
     *   tags={"store"},
     *   summary="Place an order for a pet",
     *   description="",
     *   operationId="placeOrder",
     *   @OAS\RequestBody(
     *       request="StoreOrderBody",
     *       required=true,
     *       description="order placed for purchasing the pet",
     *       @OAS\Schema(ref="#/components/schemes/Order")
     *   ),
     *   @OAS\Response(
     *     response=200,
     *     description="successful operation",
     *     @OAS\Schema(ref="#/components/schemes/Order")
     *   ),
     *   @OAS\Response(response=400, description="Invalid Order")
     * )
     */
    public function placeOrder()
    {
    }

    /**
     * @OAS\Get(path="/store/order/{orderId}",
     *   tags={"store"},
     *   summary="Find purchase order by ID",
     *   description="For valid response try integer IDs with value >= 1 and <= 10. Other values will generated exceptions",
     *   operationId="getOrderById",
     *   @OAS\Parameter(
     *     name="orderId",
     *     in="path",
     *     description="ID of pet that needs to be fetched",
     *     required=true,
     *     type="integer",
     *     format="int64",
     *     minimum=1.0,
     *     maximum=10.0,
     *   ),
     *   @OAS\Response(
     *     response=200,
     *     description="successful operation",
     *     @OAS\Schema(
     *       ref="#/definitions/Order"
     *     )
     *   ),
     *   @OAS\Response(response=400, description="Invalid ID supplied"),
     *   @OAS\Response(response=404, description="Order not found")
     * )
     */
    public function getOrderById()
    {
    }

    /**
     * @OAS\Delete(path="/store/order/{orderId}",
     *   tags={"store"},
     *   summary="Delete purchase order by ID",
     *   description="For valid response try integer IDs with positive integer value. Negative or non-integer values will generate API errors",
     *   operationId="deleteOrder",
     *   @OAS\Parameter(
     *     name="orderId",
     *     in="path",
     *     description="ID of the order that needs to be deleted",
     *     required=true,
     *     type="integer",
     *     format="int64",
     *     minimum=1.0
     *   ),
     *   @OAS\Response(response=400, description="Invalid ID supplied"),
     *   @OAS\Response(response=404, description="Order not found")
     * )
     */
    public function deleteOrder()
    {
    }
}
