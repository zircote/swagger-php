<?php

/**
 * @license Apache 2.0
 */

namespace Swagger;

use Swagger\Annotations\CustomAnnotation;

/**
 * Interface for custom annotation handling.
 */
interface CustomAnnotationHandlerInterface
{
    /**
     * Migrate a custom annotation to a *real* swagger annotation.
     *
     * @param CustomAnnotation $customAnnotation The custom annotation.
     *
     * @return array One or more swagger annotations
     */
    public function migrate(CustomAnnotation $customAnnotation);
}
