<?php
/**
 * @OA\Info(
 *   title="Testing annotations from bugreports",
 *    version="1.0.0"
 * )
 */

/**
 * @OA\Server(
 *      url="{schema}://host.dev",
 *      description="OpenApi parameters",
 *      @OA\ServerVariable(
 *          serverVariable="schema",
 *          enum="['https', 'http']",
 *          default="https"
 *      )
 * )
 */
