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
}
