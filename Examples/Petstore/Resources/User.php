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
use Swagger\Annotations as SWG;

/**
 * @package
 * @category
 *
 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   basePath="http://petstore.swagger.wordnik.com/api",
 *   resourcePath="/user",
 *   description="Operations about user",
 *   @SWG\Produces("application/json")
 * )
 */
class User
{
    /**
     * @SWG\Api(path="/user/{username}",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="Get user by user name",
     *     notes="",
     *     type="User",
     *     nickname="getUserByName",
     *     @SWG\Parameter(
     *       name="username",
     *       description="The name that needs to be fetched. Use user1 for testing.",
     *       required=true,
     *       type="string",
     *       paramType="path"
     *     ),
     *     @SWG\ResponseMessage(code=400, message="Invalid username supplied"),
     *     @SWG\ResponseMessage(code=404, message="User not found")
     *   )
     * )
     */
    public function getUserByName() {

    }

    /**
     * @SWG\Api(path="/user/login",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="Logs user into the system",
     *     notes="",
     *     type="string",
     *     nickname="loginUser",
     *     @SWG\Parameter(
     *       name="username",
     *       description="The user name for login",
     *       required=true,
     *       type="string",
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="password",
     *       description="The password for login in clear text",
     *       required=true,
     *       type="string",
     *       paramType="query"
     *     ),
     *     @SWG\ResponseMessage(code=400,message="Invalid username and password combination")
     *   )
     * )
     */
    public function loginUser() {

    }

    /**
     * @SWG\Api(path="/user/logout",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="Logs out current logged in user session",
     *     notes="",
     *     type="void",
     *     nickname="logoutUser"
     *   )
     * )
     */
    public function logoutUser() {

    }

}
