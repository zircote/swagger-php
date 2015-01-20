<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Swagger;

/**
 *
 */
class MergeSwagger {

    public function __invoke(Swagger $swagger) {

        foreach ($swagger->_unmerged as $i => $annotation) {
            if ($annotation instanceof Swagger) {
                $paths = $annotation->paths;
                unset($annotation->paths);
                $swagger->mergeProperties($annotation);
                unset($swagger->_unmerged[$i]);
                foreach ($paths as $path) {
                    $swagger->paths[] = $path;
                }
            }
        }
    }

}
