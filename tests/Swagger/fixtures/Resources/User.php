<?php
namespace SwaggerTests\Fixtures\Resources;

/**
 * @package
 * @category
 * @subpackage
 */
use Swagger\Annotations\Operation;
use Swagger\Annotations\Operations;
use Swagger\Annotations\Parameter;
use Swagger\Annotations\Parameters;
use Swagger\Annotations\Api;
use Swagger\Annotations\ErrorResponse;
use Swagger\Annotations\ErrorResponses;
use Swagger\Annotations\Resource;

/**
 * @package
 * @category
 *
 * @Resource(
 *      apiVersion="0.2",
 *      swaggerVersion="1.1",
 *      basePath="http://petstore.swagger.wordnik.com/api",
 *      resourcePath="/user"
 * )
 */
class User
{
    /**
     * @Api(
     *   resourcePath="/user",
     *   path="/user.{format}/createWithArray", description="Operations about user",
     *   @operations(
     *     @operation(
     *       httpMethod="POST", summary="Creates list of users with given input array",
     *       responseClass="void", nickname="createUsersWithArrayInput",
     *       @parameters(
     *         @parameter(
     *           description="List of user object", paramType="body",
     *           required="true", allowMultiple=false, dataType="Array[User]"
     *         )
     *       )
     *     )
     *   )
     * )
     */
    public function createUsersWithArrayInput()
    {
    }

    /**
     *
     * @Api(
     *   resourcePath="/user",
     *   path="/user.{format}", description="Operations about user",
     *   @operations(
     *     @operation(
     *       httpMethod="POST", summary="Creates user",
     *       notes="This can only be done by the logged in user.",
     *       responseClass="void", nickname="createUser",
     *       @parameters(
     *         @parameter(
     *           description="Created user object", paramType="body",
     *           required="true", allowMultiple=false, dataType="User"
     *         )
     *       )
     *     )
     *   )
     * )
     */
    public function createUser()
    {
    }

    /**
     *
     * @Api(
     *   resourcePath="/user",
     *   path="/user.{format}/createWithList", description="Operations about user",
     *   @operations(
     *     @operation(
     *       httpMethod="POST", summary="Creates list of users with given list input",
     *       responseClass="void", nickname="createUser",
     *       @parameters(
     *         @parameter(
     *           description="List of user object", paramType="body",
     *           required="true", allowMultiple=false, dataType="List[User]"
     *         )
     *       )
     *     )
     *   )
     * )
     */
    protected function createUsersWithListInput()
    {
    }

    /**
     * @Api(
     *   resourcePath="/user",
     *   path="/user.{format}/{username}", description="Operations about user",
     *   @operations(
     *     @operation(
     *       httpMethod="PUT", summary="Updated user",
     *       notes="This can only be done by the logged in user.",
     *       responseClass="void", nickname="createUser",
     *       @parameters(
     *         @parameter(
     *           name="username", description="name that need to be updated",
     *           required="true", allowMultiple=false, dataType="string", paramType="path"
     *         ), @parameter(
     *              description="Updated user object", paramType="body",
     *              required="true", allowMultiple=false, dataType="User"
     *         )
     *       ),
     *       @errorResponses(
     *          @errorResponse(code="400", reason="Invalid username supplied"),
     *          @errorResponse(code="404", reason="User not found")
     *       )
     *     )
     *   )
     * )
     */
    public function updateUser()
    {
    }

    /**
     * @Api(
     *   resourcePath="/user",
     *   path="/user.{format}/{username}", description="Operations about user",
     *   @operations(
     *     @operation(
     *       httpMethod="DELETE", summary="Delete user",
     *       notes="This can only be done by the logged in user.",
     *       responseClass="void", nickname="deleteUser",
     *       @parameters(
     *         @parameter(
     *           description="The name that needs to be deleted", paramType="path",
     *           required="true", allowMultiple=false, dataType="string"
     *         )
     *       ),
     *       @errorResponses(
     *          @errorResponse(code="400", reason="Invalid username supplied"),
     *          @errorResponse(code="404", reason="User not found")
     *       )
     *     )
     *   )
     * )
     */
    public function deleteUser()
    {
    }

    /**
     * @Api(
     *   resourcePath="/user",
     *   path="/user.{format}/{username}", description="Operations about user",
     *   @operations(
     *     @operation(
     *       httpMethod="GETE", summary="Get user by user name",
     *       notes="This can only be done by the logged in user.",
     *       responseClass="User", nickname="getUserByName",
     *       @parameters(
     *         @parameter(
     *           description="The name that needs to be fetched. Use user1 for testing.",
     *           paramType="path", required="true", allowMultiple=false, dataType="string"
     *         )
     *       ),
     *       @errorResponses(
     *          @errorResponse(code="400", reason="Invalid username supplied"),
     *          @errorResponse(code="404", reason="User not found")
     *       )
     *     )
     *   )
     * )
     */
    public function getUserByName()
    {
    }

    /**
     * @Api(
     *   resourcePath="/user",
     *   path="/user.{format}/login", description="Operations about user",
     *   @operations(
     *     @operation(
     *       httpMethod="GET", summary="Logs user into the system",
     *       responseClass="string", nickname="loginUser",
     *       @parameters(
     *         @parameter(
     *           name="username", description="The user name for login", paramType="query",
     *           required="true", allowMultiple=false, dataType="string"
     *         ),
     *         @parameter(
     *           name="password", description="The password for login in clear text", paramType="query",
     *           required="true", allowMultiple=false, dataType="string"
     *         )
     *       ),
     *       @errorResponses(
     *          @errorResponse(code="400", reason="Invalid username and password combination")
     *       )
     *     )
     *   )
     * )
     */
    public function userLogin()
    {
    }

    /**
     * @Api(
     *   resourcePath="/user",
     *   path="/user.{format}/logout", description="Operations about user",
     *   @operations(
     *     @operation(
     *       httpMethod="GET", summary="Logs out current logged in user session",
     *       responseClass="void", nickname="logoutUser"
     *     )
     *   )
     * )
     */
    public function logoutUser()
    {
    }
}

