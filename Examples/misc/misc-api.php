<?php
/**
 * @OAS\Info(
 *   title="Testing annotations from bugreports",
 *    version="1.0.0"
 * )
 */

/**
 *  @OAS\Server(
 *      url="{schema}://host.dev",
 *      description="OpenApi parameters",
 *      @OAS\ServerVariable(
 *          serverVariable="schema",
 *          enum="['https', 'http']",
 *          default="https"
 *      )
* )
*/