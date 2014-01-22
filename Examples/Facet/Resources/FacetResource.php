<?php
namespace Facet\Resources;

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
 *  swaggerVersion="1.2",
 *  resourcePath="/facet",
 *  basePath="http://facetstore.zircote.com/swagger-php/api"
 * )
 */
class FacetResource
{
    /**
     *
     * @SWG\Api(
     *   path="/facet.{format}/{facetId}",
     *   description="Operations about facets",
     *   produces="['application/json']",
     *   @SWG\Operations(
     *     @SWG\Operation(
     *       method="GET",
     *       summary="Find facet by ID",
     *       notes="Returns a facet based on ID",
     *       type="FacetResult",
     *       nickname="getfacetById",
     *       @SWG\Parameters(
     *         @SWG\Parameter(
     *           name="facetId",
     *           description="ID of facet that needs to be fetched",
     *           paramType="path",
     *           required=true,
     *           type="string"
     *         )
     *       ),
     *       @SWG\ResponseMessages(
     *          @SWG\ResponseMessage(
     *            code=400,
     *            message="Invalid ID supplied"
     *          ),
     *          @SWG\ResponseMessage(
     *            code=404,
     *            message="facet not found"
     *          )
     *       )
     *     )
     *   )
     * )
     */
    public function getAction()
    {
    }
    /**
     *
     * @SWG\Api(
     *   path="/facet.{format}/{facetId}",
     *   description="Operations about facets",
     *   @SWG\Operations(
     *     @SWG\Operation(
     *       method="DELETE",
     *       summary="Find facet by ID",
     *       notes="Returns a facet based on ID",
     *       nickname="getfacetById",
     *       @SWG\Parameters(
     *         @SWG\Parameter(
     *           name="facetId",
     *           description="ID of facet that needs to be fetched",
     *           paramType="path",
     *           required=true,
     *           type="string"
     *         )
     *       ),
     *       @SWG\ResponseMessages(
     *          @SWG\ResponseMessage(
     *            code=400,
     *            message="Invalid ID supplied"
     *          ),
     *          @SWG\ResponseMessage(
     *            code=404,
     *            message="facet not found"
     *          )
     *       )
     *     )
     *   )
     * )
     */
    public function deleteAction()
    {
    }

    /**
     * @SWG\Api(
     *   path="/$ref:model/",
     *   description="allow namespaces and underscores in model names",
     *   @SWG\Operation(
     *     method="GET",
     *     type="array",
     *     items="$ref: Examples\Some_Model"
     *   )
     * )
     */
    public function modelReferencing()
    {
    }
}
