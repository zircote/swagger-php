<?php

/**
 * @license Apache 2.0
 */

namespace Swagger;

/**
 * Data container for all annotations found from a single class.
 */
class ClassAnnotations
{
    /** @var ReflectedAnnotations */
    public $classAnnotations;
    /** @var ReflectedAnnotations[] */
    public $methodAnnotations;
    /** @var ReflectedAnnotations[] */
    public $propertyAnnotations;

    public function __construct($classAnnotations, $methodAnnotations, $propertyAnnotations)
    {
        $this->classAnnotations    = $classAnnotations;
        $this->methodAnnotations   = $methodAnnotations;
        $this->propertyAnnotations = $propertyAnnotations;
    }

    public function getAnnotations()
    {
        $classAnnotations = $this->classAnnotations->getSwaggerAnnotations();
        $subAnnotations   = $this->getSubAnnotations();

        if (count($classAnnotations) > 0) {
            // Method and property annotations get merged with the first class annotation.
            $rootAnnotation = reset($classAnnotations);
            $rootAnnotation->merge($subAnnotations);

            return $classAnnotations;
        } else {
            return $subAnnotations;
        }
    }

    private function getSubAnnotations()
    {
        $getAnnotations = function (ReflectedAnnotations $container) { return $container->getSwaggerAnnotations(); };

        return array_reduce(
            array_merge(
                array_map($getAnnotations, $this->methodAnnotations),
                array_map($getAnnotations, $this->propertyAnnotations)
            ),
            'array_merge',
            []
        );
    }
}
