<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 *
 * A wrapper for custom annotation.
 */
class CustomAnnotation extends AbstractAnnotation
{
    /**
     * The custom annotation.
     *
     * @var mixed
     */
    public $annotation;

    /**
     * @param array $properties
     * @param mixed $annotation The custom annotation.
     */
    public function __construct($properties, $annotation = null)
    {
        parent::__construct($properties);
        $this->annotation = $annotation;
    }
}
