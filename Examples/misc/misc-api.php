<?php
/**
 * @OA\Info(
 *   title="Testing annotations from bugreports",
 *    version="1.0.0"
 * )
 */

/**
 * @OA\Get(
 *   path="/api/endpoint",
 *   @OA\Response(response=200, description="Success")
 * )
 */
  
/**
 * @OA\Server(
 *      url="{schema}://host.dev",
 *      description="OpenApi parameters",
 *      @OA\ServerVariable(
 *          serverVariable="schema",
 *          enum={"https", "http"},
 *          default="https"
 *      )
 * )
 */
