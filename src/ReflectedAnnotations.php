<?php

/**
 * @license Apache 2.0
 */

namespace Swagger;

use Swagger\Annotations\AbstractAnnotation;

/**
 * Data container for a single reflection target and related annotations.
 */
class ReflectedAnnotations
{
    /** @var \ReflectionClass|\ReflectionMethod|\ReflectionProperty */
    public $target;
    /** @var AbstractAnnotation[]|object[] */
    public $annotations = [];

    public function __construct($target, array $annotations)
    {
        $this->target      = $target;
        $this->annotations = $annotations;
    }

    /**
     * @return AbstractAnnotation[]
     */
    public function getSwaggerAnnotations()
    {
        $isSwaggerAnnotation = function ($annotation) { return $annotation instanceof AbstractAnnotation; };

        return array_filter($this->annotations, $isSwaggerAnnotation);
    }
}
