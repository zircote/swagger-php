<?php
/**
 * @SWG\Resource(
 *   description="Operations about store",
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   basePath="http://petstore.swagger.wordnik.com/api",
 *   resourcePath="/store",
 *   @SWG\Produces("application/json"),
 *   @SWG\Api(
 *     path="/store/order/{orderId}",
 *     @SWG\Operation(
 *       method="DELETE",
 *       summary="Delete purchase order by ID",
 *       notes="For valid response try integer IDs with value < 1000.  Anything above 1000 or nonintegers will generate API errors",
 *       type="void",
 *       nickname="deleteOrder",
 *       authorizations="oauth2.write:pets",
 *       @SWG\Parameter(
 *         name="orderId",
 *         description="ID of the order that needs to be deleted",
 *         required=true,
 *         type="string",
 *         paramType="path",
 *         allowMultiple=false
 *       ),
 *       @SWG\ResponseMessage(code=400, message="Invalid ID supplied"),
 *       @SWG\ResponseMessage(code=404, message="Order not found")
 *     ),
 *     @SWG\Operation(
 *       method="GET",
 *       summary="Find purchase order by ID",
 *       notes="For valid response try integer IDs with value <= 5. Anything above 5 or nonintegers will generate API errors",
 *       type="Order",
 *       nickname="getOrderById",
 *       authorizations={},
 *       @SWG\Parameter(
 *         name="orderId",
 *         description="ID of pet that needs to be fetched",
 *         required=true,
 *         type="string",
 *         paramType="path",
 *         allowMultiple=false
 *       ),
 *       @SWG\ResponseMessage(code=400, message="Invalid ID supplied"),
 *       @SWG\ResponseMessage(code=404, message="Order not found"),
 *     )
 *   ),
 *   @SWG\Api(
 *     path="/store/order",
 *     @SWG\Operation(
 *       method="POST",
 *       summary="Place an order for a pet",
 *       notes="",
 *       type="void",
 *       nickname="placeOrder",
 *       authorizations="oauth2.write:pets",
 *       @SWG\Parameter(
 *         name="body",
 *         description="order placed for purchasing the pet",
 *         required=true,
 *         type="Order",
 *         paramType="body",
 *         allowMultiple=false
 *       ),
 *       @SWG\ResponseMessage(code=400, message="Invalid order")
 *     )
 *   )
 * )
 *
 * @SWG\Model(
 *   id="Order",
 *   @SWG\Property(
 *     name="id",
 *     type="integer",
 *     format="int64"
 *   ),
 *   @SWG\Property(
 *     name="petId",
 *     type="integer",
 *     format="int64"
 *   ),
 *   @SWG\Property(
 *     name="quantity",
 *     type="integer",
 *     format="int32"
 *   ),
 *   @SWG\Property(
 *     name="status",
 *     type="string",
 *     description="Order Status",
 *     enum={"placed", " approved", " delivered"}
 *   ),
 *   @SWG\Property(
 *     name="shipDate",
 *     type="string",
 *     format="date-time"
 *   )
 * )
 */