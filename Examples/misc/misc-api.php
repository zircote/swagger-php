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
 *   @OA\Parameter(name="filter",in="query", @OA\JsonContent(
 *      @OA\Property(property="type", type="string"),
 *      @OA\Property(property="color", type="string"),
 *   )),
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

/**
 * @OA\Response(
 *     response=200,
 *     description="",
 *     @OA\MediaType(
 *          mediaType="application/json",
 *          @OA\Schema(
 *              @OA\Property(property="name", type="integer", description="demo")
 *          ),
 *          @OA\Examples(example=200, summary="", value={"name":1}),
 *          @OA\Examples(example=300, summary="", value={"name":1}),
 *          @OA\Examples(example=400, summary="", value={"name":1})
 *     )
 *   )
 */

