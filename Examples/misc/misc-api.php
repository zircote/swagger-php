<?php
/**
 * @OA\Info(
 *     title="Testing annotations from bugreports",
 *     description="Misc examples",
 *     version="1.0.0",
 *     @OA\Contact(
 *         email="bugs@swagger.php"
 *     )
 * )
 */

/**
 * @OA\Get(
 *     path="/api/endpoint",
 *     description="Api endpoint",
 *     tags={"api", "other"},
 *     operationId="endpoint",
 *     @OA\Parameter(name="filter",in="query", @OA\JsonContent(
 *         @OA\Property(property="type", type="string"),
 *         @OA\Property(property="color", type="string"),
 *    )),
 *    @OA\Response(response=200, description="Success")
 * )
 */
  
/**
 * @OA\Server(
 *     url="{schema}://host.dev",
 *     description="OpenApi server details",
 *     @OA\ServerVariable(
 *         serverVariable="schema",
 *         enum={"https", "http"},
 *         default="https"
 *     )
 * )
 */

/**
 * @OA\Tag(
 *     name="api",
 *     description="All API endpoints"
 * )
 * @OA\Tag(
 *     name="other",
 *     description="Other tag"
 * )
 */