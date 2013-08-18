<?php
namespace SwaggerTests\Fixtures3;
use Swagger\Annotations as SWG;
/**
 * @SWG\Resource(
 *      resourcePath="/logs",
 *      @SWG\Api(
 *          path="/",
 *          @SWG\Operation(
 *              httpMethod="GET",
 *              nickname="logidx",
 *              @SWG\Partial("logs.index")
 *          )
 *      ),
 *      @SWG\Partial("log_create"),
 *      @SWG\Partial("log_read")
 * )
 */
Router::route('/users/', array('controller' => 'Operations'));
?>