<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Path;
use Swagger\Annotations\Swagger;
use Swagger\Logger;

/**
 *
 */
class SwaggerPaths {

    public function __invoke(Swagger $swagger) {
        foreach ($swagger->paths as $i => $annotation) {
            if (is_int($i) === false) {
                continue;
            }
            unset($swagger->paths[$i]);
            if (empty($annotation->path)) {
                Logger::warning('Missing path for '.$annotation->identity().' in '.$annotation->_context);
            } elseif (isset($this->paths[$annotation->path])) {
                $swagger->paths->merge([$annotation]);
            } else {
                if ($annotation instanceof Path) {
                    $swagger->paths[$annotation->path] = $annotation;
                } else {
                    $swagger->paths[$annotation->path] = new Path(['path' => $annotation->path]);
                    $swagger->paths[$annotation->path]->merge([$annotation]);
                }
            }
        }
    }

}
