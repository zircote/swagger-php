<?php
namespace Petstore\Resources;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2013] [Robert Allen]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package
 * @category
 * @subpackage
 */
use Swagger\Annotations\Operation;
use Swagger\Annotations\Operations;
use Swagger\Annotations\Parameter;
use Swagger\Annotations\Parameters;
use Swagger\Annotations\Api;
use Swagger\Annotations\ResponseMessage;
use Swagger\Annotations\ResponseMessages;
use Swagger\Annotations\Resource;

/**
 * @package
 * @category
 * @subpackage
 * @Resource(apiVersion="0.2",swaggerVersion="1.2",
 * basePath="http://petstore.swagger.wordnik.com/api",resourcePath="/store")
 */
class Store
{

    /**
     *
     * @Api(
     *   path="/store.{format}/order/{orderId}",
     *   description="Operations about store",
     *   @operations(
     *     @operation(
     *       method="GET",
     *       summary="Find purchase order by ID",
     *       notes="For valid response try integer IDs with value <= 5. Anything above 5 or nonintegers will generate API errors",
     *       type="Order",
     *       nickname="getOrderById",
     *       @parameters(
     *         @parameter(
     *           name="orderId",
     *           description="ID of pet that needs to be fetched",
     *           paramType="path",
     *           required="true",
     *           type="string"
     *         )
     *       ),
     *       @responseMessages(
     *          @responseMessage(
     *            code="400",
     *            message="Invalid ID supplied"
     *          ),
     *          @responseMessage(
     *            code="404",
     *            message="Order not found"
     *          )
     *       )
     *     )
     *   )
     * )
     */
    public function getOrderById()
    {
    }

    /**
     *
     * @Api(
     *   path="/store.{format}/order/{orderId}",
     *   description="Operations about store",
     *   @operations(
     *     @operation(
     *       method="DELETE",
     *       summary="Delete purchase order by ID",
     *       notes="For valid response try integer IDs with value < 1000. Anything above 1000 or nonintegers will generate API errors",
     *       type="void",
     *       nickname="deleteOrder",
     *       @parameters(
     *         @parameter(
     *           name="orderId",
     *           description="ID of the order that needs to be deleted",
     *           paramType="path",
     *           required="true",
     *           type="string"
     *         )
     *       ),
     *       @responseMessages(
     *          @responseMessage(
     *            code="400",
     *            message="Invalid ID supplied"
     *          ),
     *          @responseMessage(
     *            code="404",
     *            message="Order not found"
     *          )
     *       )
     *     )
     *   )
     * )
     */
    public function deleteOrder()
    {
    }

    /**
     *
     * @Api(
     *   path="/store.{format}/order",
     *   description="Operations about store",
     *   @operations(
     *     @operation(
     *       method="POST",
     *       summary="Place an order for a pet",
     *       type="void",
     *       nickname="placeOrder",
     *       @parameters(
     *         @parameter(
     *           description="order placed for purchasing the pet",
     *           paramType="body",
     *           required="true",
     *           type="Order"
     *         )
     *       ),
     *       @responseMessages(
     *          @responseMessage(
     *            code="400",
     *            message="Invalid order"
     *          )
     *       )
     *     )
     *   )
     * )
     */
    public function placeOrder()
    {
    }
}

