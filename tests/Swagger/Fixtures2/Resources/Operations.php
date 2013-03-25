<?php
namespace SwaggerTests\Fixtures2\Resources;

/**
 * @package
 * @category
 * @subcategory
 */
use Swagger\Annotations as SWG;

/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *  apiVersion="0.2",
 *  swaggerVersion="1.1",
 *  resourcePath="/facet",
 *  basePath="http://f2.zircote.com/swagger-php/api"
 * )
 */
class Operations
{
    /**
     * @SWG\Api(
     *   path="/users",
     * @SWG\Operations(
     * @SWG\Operation(
     *       nickname="get_user",
     *       summary="Retrieve all users",
     *       httpMethod="GET",
     *       responseClass="User"
     *     ),
     * @SWG\Operation(
     *       nickname="create_user",
     *       summary="Create user",
     *       httpMethod="POST",
     *       responseClass="User",
     * @SWG\Parameter(name="email",dataType="String"),
     * @SWG\Parameter(name="phone",dataType="String")
     *     )
     *   )
     * )
     */
    public function index()
    {
    }
}
