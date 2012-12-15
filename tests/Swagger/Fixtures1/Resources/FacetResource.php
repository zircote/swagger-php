<?php
namespace SwaggerTests\Fixtures1\Resources;

/**
 * 
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
 *  basePath="http://facetstore.swagger.wordnik.com/api"
 * )
 */
class FacetResource
{
    /**
     *
     * @SWG\Api(
     *   path="/facet.{format}/{facetId}",
     *   description="Operations about facets",
     *   @SWG\operations(
     *     @SWG\operation(
     *       httpMethod="GET",
     *       summary="Find facet by ID",
     *       notes="Returns a facet based on ID",
     *       responseClass="FacetResult",
     *       nickname="getfacetById",
     *       @SWG\parameters(
     *         @SWG\parameter(
     *           name="facetId",
     *           description="ID of facet that needs to be fetched",
     *           paramType="path",
     *           required="true",
     *           allowMultiple="false",
     *           dataType="string"
     *         )
     *       ),
     *       @SWG\errorResponses(
     *          @SWG\errorResponse(
     *            code="400",
     *            reason="Invalid ID supplied"
     *          ),
     *          @SWG\errorResponse(
     *            code="404",
     *            reason="facet not found"
     *          )
     *       )
     *     )
     *   )
     * )
     */
    public function getAction()
    {
    }
}
