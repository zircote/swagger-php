<?php
use Swagger\Annotations as SWG;
/**
 * @SWG\Resource(resourcePath="/logs",
 *    @SWG\Partial("log_create"),
 *    @SWG\Partial("log_read")
 * )
 */

/**
 * @SWG\Api(
 *     path="/",
 *     @SWG\Operation(
 *         method="GET",
 *         nickname="logidx",
 *         @SWG\Partial("logs.index")
 *     )
 * ),
 *
 * )
 */
Router::route('/', array('controller' => 'Operations'));
?>