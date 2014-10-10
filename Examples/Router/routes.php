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
 * )
 */
Router::get('/', 'Operations@getStuff');

/**
 * @SWG\Api(
 *     path="/",
 *     @SWG\Operation(
 *         method="POST",
 *         nickname="newlog",
 *         @SWG\Partial("logs.index")
 *     )
 * )
 */
Router::post('/', 'Operation@postStuff');
?>