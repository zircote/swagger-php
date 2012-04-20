<?php
/**
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * Copyright [2012] [Robert Allen]
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
 *
 *
 * @category   Organic
 * @package    Organic_V1
 * @subpackage Controller
 */
/**
 * @SwaggerResource(
 *     basePath="http://org.local/v1",
 *     swaggerVersion="0.1a",
 *     apiVersion="1"
 * )
 * @Swagger (
 *     path="/organic",
 *     value="Gets collection of organics",
 *     description="This is a long description of what it does"
 *     )
 * @SwaggerProduces (
 *     'application/json',
 *     'application/json+hal',
 *     'application/json-p',
 *     'application/json-p+hal',
 *     'application/xml',
 *     'application/xml',
 *     'application/xml+hal'
 *     )
 *
 * @category   Organic
 * @package    Organic_V1
 * @subpackage Controller
 */
class organic_RoutesController
{
    /**
     *
     * @var string
     */
    protected $_baseUri = '/v1';
    /**
     *
     * @var string
     */
    protected $_resource = 'organic';
    /**  
     *
     * @var V1_Service_Organic_Routes
     */
    protected $_service;
    /**
     * Default Allow
     * @var array
     */
    protected $_allow = array('GET','POST', 'OPTIONS', 'HEAD');
    /**
     *
     * @see Organic_Rest_AbstractController::init()
     */
    public function init()
    {
    }
    /**
     *
     * @PUT
     * @SwaggerPath /{organic_id}
     * @SwaggerOperation(
     *     value="Updates the existing organic designated by the {organic_id}",
     *     responseClass="organic_route",
     *     multiValueResponse=false,
     *     tags="MLR"
     * )
     * @SwaggerError(code=400,reason="Invalid ID Provided")
     * @SwaggerError(code=403,reason="User Not Authorized")
     * @SwaggerError(code=404,reason="Lead Responder Not Found")
     * @SwaggerParam(
     *     description="ID of the route being requested",
     *     required=true,
     *     allowMultiple=false,
     *     dataType="integer",
     *     name="organic_id",
     *     paramType="path"
     * ) 
     * @SwaggerParam(
     *     description="organic_route being updated",
     *     required=true,
     *     allowMultiple=false,
     *     dataType="organic_route",
     *     name="organic_route",
     *     paramType="body"
     * )
     * @see Organic_Rest_AbstractController::postAction()
     */
    public function putAction()
    {
    }
}