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
 *  swaggerVersion="1.2",
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
     *     summary="Retrieve all dogs",
     *     method="GET",
     *     type="List[Dog]"
     *   )
     * )
	 *
	 * Auto-generated:
     * nickname should resolve to "get_dogs"
	 * Resolved:
	 * The model "Dog" should be in the resource->models.
     */
    public function get_dogs()
    {
    }
}
