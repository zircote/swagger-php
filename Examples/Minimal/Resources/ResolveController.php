<?php
namespace Minimal\Resources;

// swagger-php uses the @SWG namespace by default. The `use Swagger\Annotations as SWG;` statement is optional.

/**
 * @SWG\Resource(
 *  apiVersion="0.2",
 *  basePath="http://example.com/api"
 * )
 *
 * Auto-generated:
 * Uses swaggerVersion="1.2" by default.
 * classname "ResolveController" should resolve to resourcePath "/resolve".
 * 
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
     * nickname resolves to the method name "get_dogs"
     */
    public function get_dogs()
    {
    }
}
