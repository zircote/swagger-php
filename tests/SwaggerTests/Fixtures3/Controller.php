<?php
namespace SwaggerTests\Fixtures3;

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
 */
class LogsController
{
    /**
     *  @SWG\Operation(
     *      partial="logs.index",
     *      summary="This function does some magic xyz"
     *  )
     */
    public function index()
    {
    }
    /**
     * @SWG\Api(
     *   partial="log_create",
     *   path="/logs/add",
     *   @SWG\Partial("crud/create")
     * )
     */
    public function create() {

    }

    /**
     * @SWG\Api(
     *   partial="log_read",
     *   path="/logs/{id}",
     *   @SWG\Operation(
     *     @SWG\Partial("crud/read"),
     *     summary="View a log entry"
     *   )
     * )
     */
    public function read() {
        // Uses the crud/read partial, but overides the description.
    }
}
