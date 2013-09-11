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
     *       method="GET",
     *       type="User"
     *     ),
     * @SWG\Operation(
     *       nickname="create_user",
     *       summary="Create user",
     *       method="POST",
     *       type="User",
     * @SWG\Parameter(name="email",type="string"),
     * @SWG\Parameter(name="phone",type="string")
     *     )
     *   )
     * )
     */
    public function index()
    {
    }
}
