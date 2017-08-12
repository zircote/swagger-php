<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Components;
use Swagger\Analysis;
use Swagger\Context;
use Swagger\UNDEFINED;

/**
 * Merge reusable annotation into @SWG\Schemas
 */
class MergeIntoComponents
{
    public function __invoke(Analysis $analysis)
    {
        $components = $analysis->openapi->components;
        if (!$components) {
            $components = new Components([]);
            $components->_context->generated = true;
        }
        $classes = array_keys(Components::$_nested);
        foreach ($analysis->annotations as $annotation) {
            $class = get_class($annotation);
            if (in_array($class, $classes) && $annotation->_context->is('nested') === false) { // A top level annotation.
                $components->merge([$annotation], true);
                $analysis->openapi->components = $components;
            }
        }
    }
}
