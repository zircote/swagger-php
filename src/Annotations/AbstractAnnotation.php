<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

use Doctrine\Common\Annotations\Annotation;
use JsonSerializable;
use Swagger\Context;
use Swagger\Logger;
use Swagger\Parser;

abstract class AbstractAnnotation extends Annotation implements JsonSerializable {

    /**
     * Special value to allow null in the output.
     */
    const UNDEFINED = '{SWAGGER-PHP-UNDEFINED-46EC-07AB32D2-D50C}';

    /**
     * Allows extensions to the Swagger Schema.
     * The keys inside the array will be prefixed with `x-`.
     * For further details see https://github.com/swagger-api/swagger-spec/blob/master/versions/2.0.md#vendorExtensions.
     * @var array
     */
    public $x;

    /**
     * @var Context
     */
    public $_context;

    /**
     * Annotations that couldn't be merged by mapping or postprocessing.
     * @var array
     */
    public $_unmerged = [];

    /**
     * Declarative mapping of Annotation types to properties.
     * Examples:
     *   'Swagger\Annotation\Info' => 'info', // Set @SWG\Info annotation as the info property.
     *   'Swagger\Annotation\Parameter' => 'parameters[]',  // Append @SWG\Parameter annotations the parameters array.
     * @var array
     */
    public static $nested = [];

    /**
     *
     * @var string[]
     */
    public static $parents = [];

    /**
     *
     * @var array
     */
    public static $blacklist = ['_context', '_unmerged', 'value'];

    /**
     * @param Context $context
     */
    public function initialize($context) {
        $this->_context = $context;
        $this->_context->annotations[] = $this;
        if (is_array($this->value)) {
            $annotations = [];
            foreach ($this->value as $annotation) {
                if (is_object($annotation) && $annotation instanceof AbstractAnnotation) {
                    $annotation->initialize($context);
                    $annotations[] = $annotation;
                }
            }
            $this->merge($annotations);
            $this->value = null;
        }
    }

    public function __set($name, $value) {
        $context = $this->_context ? : Parser::$context;
        $properties = get_object_vars($this);
        foreach (static::$blacklist as $property) {
            unset($properties[$property]);
        }
        Logger::notice('Skipping field "' . $name . '" for ' . $this->identity() . ', expecting "' . implode('", "', array_keys($properties)) . '" in ' . $context);
    }

    /**
     * Return a identity for easy debugging.
     * Example: "@SWG\Get(path="/pets")"
     * @return string
     */
    public function identity() {
        return '@' . str_replace('Swagger\\Annotations\\', 'SWG\\', get_class($this)) . '()';
    }

    /**
     * @param AbstractAnnotation $annotations
     */
    public function merge($annotations) {
        foreach ($annotations as $annotation) {
            $found = false;
            foreach (static::$nested as $class => $property) {
                if ($annotation instanceof $class) {
                    if (substr($property, -2) === '[]') { // Append to array?
                        $property = substr($property, 0, -2);
                        if ($this->$property === null) {
                            $this->$property = [];
                        }
                        array_push($this->$property, $annotation);
                    } else {
                        if ($this->$property) { // Don't overwrite existing property
                            $this->$property->merge([$annotation]);
                        } else {
                            $this->$property = $annotation;
                        }
                    }
                    $found = true;
                    break;
                }
            }
            if ($found === false) {
                $this->_unmerged[] = $annotation;
            }
        }
    }

    /**
     *
     * @return array
     */
    public function jsonSerialize() {
        $data = [];
        // Strip null values
        $classVars = get_class_vars(get_class($this));
        foreach (get_object_vars($this) as $property => $value) {
            if ($classVars[$property] === self::UNDEFINED) {
                if ($value !== self::UNDEFINED) {
                    $data[$property] = $value;
                }
            } elseif ($value !== null) {
                $data[$property] = $value;
            }
        }
        // Strip properties that are for internal (swagger-php) use.
        foreach (static::$blacklist as $property) {
            unset($data[$property]);
        }
        // Inject vendor properties.
        unset($data['x']);
        if (is_array($this->x)) {
            foreach ($this->x as $property => $value) {
                $data['x-' . $property] = $value;
            }
        }
        return $data;
    }

    /**
     * Validate annotation tree, and log notices & warnings.
     * @return boolean
     */
    public function validate() {
        $valid = true;
        // Report orphaned annotations
        foreach ($this->_unmerged as $annotation) {
            $message = 'Unexpected ' . $annotation->identity();
            $class = get_class($annotation);
            if (count($class::$parents)) {
                $shortNotations = [];
                foreach ($class::$parents as $_class) {
                    $shortNotations[] = '@' . str_replace('Swagger\\Annotations\\', 'SWG\\', $_class);
                }
                $message .= ', expected to be inside ' . implode(', ', $shortNotations);
            }
            Logger::notice($message . ' in ' . $annotation->_context);
            $valid = false;
        }
        return self::_validate($this) ? $valid : false;
    }

    /**
     * Recursively validate all annotation properties.
     *
     * @param AbstractAnnotation $annotation
     * @return boolean
     */
    private static function _validate($annotation) {
        $valid = true;
        $properties = get_object_vars($annotation);
        foreach ($properties as $property => $value) {
            if ($value === null || is_scalar($value) || $property !== '_unmerged') {
                continue;
            }
            if (is_object($value) && method_exists($value, 'validate')) {
                if (!$valid->validate()) {
                    $valid = false;
                }
            }
        }
        if (is_array($value)) {
            foreach ($value as $item) {
                if (!self::_validate($item)) {
                    $valid = false;
                }
            }
        }
        return $valid;
    }

}
