<?php

namespace Swagger;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2013] [Robert Allen]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * @category   Swagger
 * @package    Swagger
 */

/**
 * Context
 *
 * The context in which the annotation is parsed.
 * It includes useful metadata which the Processors can use to augment the annotations.
 *
 * Context hierarchy:
 * - parseContext
 *   |- docBlockContext
 *   |- classContext
 *      |- docBlockContext
 *      |- propertyContext
 *      |- methodContext
 *
 * @property string $comment  The PHP DocComment
 * @property string $filename
 * @property int $line
 * @property int $character
 *
 * @property string $class
 * @property string $extends
 * @property string $method
 * @property string $property
 *
 * @property Annotations\Model $model
 * @property Annotations\Resource $resource
 */
class Context {

    /**
     * Prototypical inheritance for properties.
     * @var Context
     */
    private $_parent;

    /**
     * @param array $properties new properties for this context.
     * @param Context $parent The parent context
     */
    public function __construct($properties = array(), $parent = null)
    {
        foreach ($properties as $property => $value) {
            $this->$property = $value;
        }
        $this->_parent = $parent;
    }

    /**
     * Check if a property is set directly on this context and not its parent context.
     *
     * @param string $type Example: $c->is('method') or $c->is('class')
     * @return bool
     */
    public function is($type)
    {
        return property_exists($this, $type);
    }

    /**
     * Return the context containing the specified property.
     *
     * @param string $property
     * @return boolean|\Swagger\Context
     */
    public function with($property) {
        if (property_exists($this, $property)) {
            return $this;
        }
        if ($this->_parent) {
            return $this->with($property);
        }
        return false;
    }

    /**
     * Export location for debugging.
     *
     * @return string Example: "file1.php on line 12"
     */
    public function getDebugLocation() {
        $location = '';
        if ($this->class && ($this->method || $this->property)) {
            $location .= $this->class;
            if ($this->method) {
                $location .= ($this->static ? '::' : '->').$this->method.'()';
            } elseif ($this->property) {
                $location .= ($this->static ? '::$' : '->').$this->property;
            }
        }
        if ($this->filename) {
            if ($location !== '') {
                $location .= ' in ';
            }
            $location .= $this->filename;
        }
        if ($this->line) {
            if ($location !== '') {
                $location .= ' on';
            }
            $location .= ' line '.$this->line;
            if ($this->character) {
                $location .= ':'.$this->character;
            }
        }
        return $location;
    }

    /**
     * Traverse the context tree to get the property value.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        if ($this->_parent) {
            return $this->_parent->$property;
        }
        return null;
    }

    public function __toString() {
        return $this->getDebugLocation();
    }



    /**
     * @return string|null
     */
    public function extractDescription()
    {
        $lines = explode("\n", $this->comment);
        unset($lines[0]);
        $description = '';
        foreach ($lines as $line) {
            $line = ltrim($line, "\t *");
            if (substr($line, 0, 1) === '@') {
                break;
            }
            $description .= $line . ' ';
        }
        $description = trim($description);
        if ($description === '') {
            return null;
        }
        if (stripos($description, 'license')) {
            return null; // Don't use the GPL/MIT license text as the description.
        }
        return $description;
    }

    /**
     * Create a Context based on the debug_backtrace
     * @param int $index
     * @return \Swagger\Context
     */
    static public function detect($index = 0) {
        $context = new Context();
        $backtrace = debug_backtrace();
        $call = $backtrace[$index];
        if (isset($call['function'])) {
            $context->method = $call['function'];
            if (isset($call['type']) && $call['type'] === '::') {
                $context->static = true;
            }
        }
        if (isset($call['class'])) {
            $context->class = $call['class'];
        }
        if (isset($call['file'])) {
            $context->filename = $call['file'];
        }
        if (isset($call['line'])) {
            $context->line = $call['line'];
        }
        return $context;
    }

}
