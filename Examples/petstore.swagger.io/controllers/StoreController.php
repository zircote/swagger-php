<?php

namespace PetstoreIO;

abstract class StoreController
{

    /**
     * @SWG\Get(path="/store/inventory",
     *   tags={"store"},
     *   summary="Returns pet inventories by status",
     *   description="Returns a map of status codes to quantities",
     *   operationId="getInventory",
     *   produces={"application/json"},
     *   parameters={},
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(
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
     * @SWG\Post(path="/store/order",
     *   tags={"store"},
     *   summary="Place an order for a pet",
     *   description="",
     *   operationId="placeOrder",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="order placed for purchasing the pet",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/Order")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(ref="#/definitions/Order")
     *   ),
     *   @SWG\Response(response=400,  description="Invalid Order")
     * )
     */
    public function placeOrder()
    {
    }

    /**
     * @SWG\Get(path="/store/order/{orderId}",
     *   tags={"store"},
     *   summary="Find purchase order by ID",
     *   description="For valid response try integer IDs with value >= 1 and <= 10. Other values will generated exceptions",
     *   operationId="getOrderById",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     name="orderId",
     *     in="path",
     *     description="ID of pet that needs to be fetched",
     *     required=true,
     *     type="integer",
     *     format="int64",
     *     minimum=1.0,
     *     maximum=10.0,
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(
     *       ref="#/definitions/Order"
     *     )
     *   ),
     *   @SWG\Response(response=400, description="Invalid ID supplied"),
     *   @SWG\Response(response=404, description="Order not found")
     * )
     */
    public function getOrderById()
    {
    }

    /**
     * @SWG\Delete(path="/store/order/{orderId}",
     *   tags={"store"},
     *   summary="Delete purchase order by ID",
     *   description="For valid response try integer IDs with positive integer value. Negative or non-integer values will generate API errors",
     *   operationId="deleteOrder",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     name="orderId",
     *     in="path",
     *     description="ID of the order that needs to be deleted",
     *     required=true,
     *     type="integer",
     *     format="int64",
     *     minimum=1.0
     *   ),
     *   @SWG\Response(response=400, description="Invalid ID supplied"),
     *   @SWG\Response(response=404, description="Order not found")
     * )
     */
    public function deleteOrder()
    {
    }
}
