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
 * @apiresource(
 *     basePath="http://org.local/v1",
 *     swaggerVersion="0.1a",
 *     apiVersion="1"
 * )
 * @Api (
 *     path="/leadresponder",
 *     value="Gets collection of leadresponders",
 *     description="This is a long description of what it does"
 *     )
 * @ApiProduces (
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
class LeadResponder_RoutesController
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
    protected $_resource = 'leadresponder';
    /**
     *
     * @var V1_Service_LeadResponder_Routes
     */
    protected $_service;
    /**
     * Default Allow
     * @var array
     */
    protected $_allow = array('GET','POST', 'OPTIONS', 'HEAD');
    /**
     *
     * @see Ifbyphone_Rest_AbstractController::init()
     */
    public function init()
    {
    }
    /**
     *
     * @PUT
     * @ApiPath /{leadresponder_id}
     * @ApiOperation(
     *     value="Updates the existing leadresponder designated by the {leadresponder_id}",
     *     responseClass="leadresonder_route",
     *     multiValueResponse=false,
     *     tags="MLR"
     * )
     * @ApiError(code=400,reason="Invalid ID Provided")
     * @ApiError(code=403,reason="User Not Authorized")
     * @ApiError(code=404,reason="Lead Responder Not Found")
     * @ApiParam(
     *     description="ID of the leadresponder being requested",
     *     required=true,
     *     allowMultiple=false,
     *     dataType="integer",
     *     name="leadresponder_id",
     *     paramType="path"
     * )
     * @ApiParam(
     *     description="leadresponder_route being updated",
     *     required=true,
     *     allowMultiple=false,
     *     dataType="leadresponder_route",
     *     name="leadresponder_route",
     *     paramType="body"
     * )
     * @see Ifbyphone_Rest_AbstractController::postAction()
     */
    public function putAction()
    {
    }
}