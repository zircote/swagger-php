<?php
/**
 *
 *
 *
 * @category   Organic
 * @package    Organic_V1
 * @subpackage Controller
 */
/**
 *
 * @Path /leadresponder
 * @Api /leadresponder
 * @Produces ('application/json',
 *     'application/json+hal',
 *     'application/json-p',
 *     'application/json-p+hal',
 *     'application/xml',
 *     'application/xml',
 *     'application/xml+hal'
 * )
 *
 * @category   Organic
 * @package    Organic_V1
 * @subpackage Controller
 */
class LeadResponder_RoutesIdController
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
     * @GET
     * @Path /{leadresponder_id}
     * @ApiOperation(
     *     value="Fetches the leadresponder corresponding the the provided ID",
     *     responseClass="Ifbyphone_Model_LeadResponder_RouteCollection",
     *     multiValueResponse=false,
     *     tags="MLR"
     * )
     * @ApiError(code=400,reason="Invalid ID Provided")
     * @ApiError(code=403,reason="User Not Authorized")
     * @ApiError(
     *     code=404,
     *     reason="Lead Responder Not Found"
     *  )
     * @ApiParam(
     *     description="ID of the leadresponder being requested",
     *     required=true,
     *     allowMultiple=false,
     *     allowedValues="allowableId1,allowableId2",
     *     dataType="integer",
     *     name="leadresponder_id",
     *     paramType="path"
     * )
     * @see Ifbyphone_Rest_AbstractController::getAction()
     */
    public function getAction()
    {
    }
    /**
     *
     * @see Ifbyphone_Rest_AbstractController::postAction()
     */
    public function postAction()
    {
    }
}