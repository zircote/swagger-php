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
        $classes = array_keys(Components::$_nested);
        foreach ($analysis->annotations as $annotation) {
            $class = get_class($annotation);
            if (in_array($class, $classes) && $annotation->_context->is('nested') === false) { // A top level annotation.
                list($collection, $property) = Components::$_nested[$class];
                // $value = $annotation->$property;
                // if ($value === null || $value === UNDEFINED) {
                //     continue;
                // }
                array_push($components->$collection, $annotation);
            }
        }
    }
}
