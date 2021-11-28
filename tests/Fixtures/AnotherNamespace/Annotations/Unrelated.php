<?php

/**
 * @license Apache 2.0
 */

namespace AnotherNamespace\Annotations;

/**
 * Unrelated annotation.
 *
 * @Annotation
 */
class Unrelated
{
    protected $s;

    public function __construct($s = null)
    {
        $this->s = $s;
    }
}