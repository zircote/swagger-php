<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Property;
use Swagger\Annotations\Swagger;
use Swagger\Annotations\Definition;
use Swagger\Annotations\Items;
use Swagger\Context;

/**
 * Copy the annotated properties from parent classes;
 */
class InheritProperties {

    public function __invoke(Swagger $swagger) {
        $children = $this->getChildren($swagger);
        $definitions = $this->getDefinitions($swagger);

        foreach ($swagger->_unmerged as $i => $property) {
            if ($property instanceof Property && $property->_context->is('property') && $property->property !== null) {
                $class = $property->_context->fullyQualifiedName($property->_context->class);
                if (isset($children[$class])) {
                    $merged = false;
                    foreach ($children[$class] as $child) {
                        if (isset($definitions[$child])) {
                            $this->mergeProperties($definitions[$child], [$property]);
                            $merged = true;
                        }
                    }
                    if ($merged) {
                        unset($swagger->_unmerged[$i]);
                    }
                }
            }
        }
        foreach ($definitions as $class => $definition) {
            if (isset($children[$class]) && count($definition->properties) > 0) {
                foreach ($children[$class] as $child) {
                    if (isset($definitions[$child])) {
                        $this->mergeProperties($definitions[$child], $definition->properties);
                    }
                }
            }
        }
    }

    /**
     * @param array $swagger
     * @return array '\Namespace\ClassName' => SWG\Definition
     */
    public function getDefinitions($swagger) {
        $definitions = [];
        foreach ($swagger->definitions as $definition) {
            if ($definition->_context->is('class')) {
                $definitions[$definition->_context->fullyQualifiedName($definition->_context->class)] = $definition;
            }
        }
        return $definitions;
    }

    /**
     * @param Swagger $swagger
     * @return array '\namespace\ParentClass' => ['\namespace\Child', 'Child2']
     */
    public function getChildren($swagger) {
        $extends = []; // '\namespace\class' => '\namespace\parent'
        foreach ($swagger->definitions as $definition) {
            $context = $definition->_context;
            if ($context->is('extends')) {
                $extends[$context->fullyQualifiedName($context->class)] = $context->fullyQualifiedName($context->extends);
            }
        }
        foreach ($swagger->_unmerged as $property) {
            
            if ($property instanceof Property && $property->_context->is('property')) {
                $context = $property->_context->with('class');
                if ($context && $context->is('extends')) {
                    $extends[$context->fullyQualifiedName($context->class)] = $context->fullyQualifiedName($context->extends);
                }
            }
        }
        $children = [];
        foreach ($extends as $class => $parent) {
            if (array_key_exists($parent, $children) === false) {
                $children[$parent] = $this->getChildrenFor($parent, $extends);
            }
        }
        return $children;
    }

    /**
     * @param string $class
     * @param array $extends '\namespace\class' => '\namespace\parent'
     * @param array
     */
    private function getChildrenFor($class, $extends) {
        $children = [];
        foreach ($extends as $child => $parent) {
            if ($parent === $class) { // A direct descendant?
                $children[] = $child;
                $children = array_merge($children, $this->getChildrenFor($child, $extends));
            }
        }
        return array_unique($children);
    }

    /**
     * 
     * @param Definition $definition
     * @param Property[] $properties
     */
    private function mergeProperties($definition, $properties) {
        if ($definition->properties === null) {
            $definition->properties = $properties;
            return;
        }
        foreach ($properties as $property) {
            $unique = true;
            foreach ($definition->properties as $existingProperty) {
                if ($existingProperty->property === $property->property) {
                    $unique = false;
                }
            }
            if ($unique) {
                $definition->properties[] = $property;
            }
        }
    }

}
