<?php

/**
 * @license Apache 2.0
 */

namespace Swagger;

use Closure;
use Exception;
use Swagger\Annotations\Swagger;
use Swagger\Processors\AugmentParameter;
use Swagger\Processors\BuildPaths;
use Swagger\Processors\ClassProperties;
use Swagger\Processors\InheritProperties;
use Swagger\Processors\MergeSwagger;

/**
 * Registry for the post-processing operations.
 */
class Processing {

    /**
     * Apply all processors 
     * @param Swagger $swagger
     */
    static function process($swagger) {
        foreach (self::processors() as $processor) {
            $processor($swagger);
        }
    }

    /**
     * @var Closure[]
     */
    private static $processors;

    /**
     * Get direct access to the processors array.
     * @return array reference
     */
    static function &processors() {
        if (!self::$processors) {
            // Add default processors.
            self::$processors = [
                new MergeSwagger(),
                new BuildPaths(),
                new ClassProperties(),
                new InheritProperties(),
                new AugmentParameter(),
            ];
        }
        return self::$processors;
    }

    /**
     * Register a processor
     * @param Closure $processor
     */
    static function register($processor) {
        array_push(self::processors(), $processor);
    }
    
    /**
     * Unregister a processor
     * @param Closure $processor
     */
    static function unregister($processor) {
        $processors = &self::processors();
        $key = array_search($processor, $processors, true);
        if ($key === false) {
            throw new Exception('Given processor was not registered');
        }
        unset($processors[$key]);
    }

}
