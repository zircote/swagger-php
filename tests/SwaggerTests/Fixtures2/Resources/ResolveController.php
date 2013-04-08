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
 *  basePath="http://example.com//api"
 * )
 *
 * Auto-generated:
 * classname "ResolveController" should resolve to resourcePath "/resolve"
 */
class ResolveController
{
    /**
     * @SWG\Api(
     *   path="/resolve",
     *   @SWG\Operation(
     *     summary="Retrieve all pets",
     *     httpMethod="GET",
     *     responseClass="List[Pet]"
     *   )
     * )
	 *
	 * Auto-generated:
     * nickname should resolve to "get_pets"
	 * Resolved:
	 * The model "Pet" should be in the resource->models.
     */
    public function get_pets()
    {
    }
}
