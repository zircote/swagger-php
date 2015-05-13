<?php

/**
 * @license Apache 2.0
 */

namespace Swagger;

use Swagger\Annotations\AbstractAnnotation;

interface AnalyserInterface
{
    /**
     * Extract and process all annotations from a file.
     *
     * @param string $filename Path to a php file.
     *
     * @return AbstractAnnotation[]
     */
    public function fromFile($filename);
}
