<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

use JsonSerializable;
use stdClass;
use Swagger\Context;
use Swagger\Logger;
use Swagger\Parser;

/**
 * The swagger annotation base class.
 */
abstract class AbstractAnnotation implements JsonSerializable {

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
    public static $_nested = [];

    /**
     * Reverse mapping of $_nested with the allowed parent annotations.
     * @var string[]
     */
    public static $_parents = [];

    /**
     * List of properties are blacklisted from the JSON output.
     * @var array
     */
    public static $_blacklist = ['_context', '_unmerged'];

    /**
     * The property that is used as key in the output json.
     * @var string
     */
    public static $_key = false;

    /**
     * @param array $properties
     */
    public function __construct($properties) {
        if (Parser::$context) {
            $this->_context = Parser::$context;
        } else {
            $this->_context = Context::detect(1);
            $this->_context->annotations = [];
        }
        $this->_context->annotations[] = $this;
        foreach ($properties as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            } elseif ($property !== 'value') {
                $this->$property = $value;
            } elseif (is_array($value)) {
                $annotations = [];
                foreach ($value as $annotation) {
                    if (is_object($annotation) && $annotation instanceof AbstractAnnotation) {
                        $annotations[] = $annotation;
                    } else {
                        Logger::notice('Unexpected field in ' . $this->identity());
                    }
                }
                $this->merge($annotations);
            } elseif (is_object($value)) {
                $this->merge([$value]);
            } else {
                Logger::notice('Unexpected parameter in ' . $this->identity());
            }
        }
    }

    public function __get($property) {
        $properties = get_object_vars($this);
        Logger::notice('Property "' . $property . '" doesn\'t exist in a ' . $this->identity() . ', exising properties: "' . implode('", "', array_keys($properties)) . '" in ' . $this->_context);
    }

    public function __set($property, $value) {
        $fields = get_object_vars($this);
        foreach (static::$_blacklist as $_property) {
            unset($fields[$_property]);
        }
        Logger::notice('Unexpected field "' . $property . '" for ' . $this->identity() . ', expecting "' . implode('", "', array_keys($fields)) . '" in ' . $this->_context);
        $this->$property = $value;
    }

    /**
     * Merge given annotations to their mapped properties configured in static::$_nested.
     * Annotations that couldn't be merged are added to the _unmerged array.
     *
     * @param AbstractAnnotation[] $annotations
     */
    public function merge(Array $annotations) {
        foreach ($annotations as $annotation) {
            $found = false;
            foreach (static::$_nested as $class => $property) {
                if ($annotation instanceof $class) {
                    if (substr($property, -2) === '[]') { // Append to array?
                        $property = substr($property, 0, -2);
                        if ($this->$property === null) {
                            $this->$property = [];
                        }
                        array_push($this->$property, $annotation);
                    } else {
                        if ($this->$property) { // Don't overwrite existing property
                            $this->_unmerged[] = $annotation;
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
     * Merge the properties from the given object into this annotation.
     * Prevents overwriting properties that are already configured.
     *
     * @param stdClass $object
     */
    public function mergeProperties($object) {
        $defaultValues = get_class_vars(get_class($this));
        $currentValues = get_object_vars($this);
        foreach ($object as $property => $value) {
            if ($property === '_context') {
                continue;
            }
            if ($currentValues[$property] === $defaultValues[$property]) { // Overwrite default values
                $this->$property = $value;
                continue;
            }
            if ($property === '_unmerged') {
                $this->_unmerged = array_merge($this->_unmerged, $value);
                continue;
            }
            if ($currentValues[$property] !== $value) { // New value is not the same?
                if ($defaultValues[$property] === $value) { // but is the same as the default?
                    continue; // Keep current, no notice
                }
                $context = property_exists($object, '_context') ? $object->_context : 'unknown';
                $identity = method_exists($object, 'identity') ? $object->identity() : get_class($object);
                Logger::warning('Skipping field "' . $property . '" in ' . $identity . ' in ' . $context);
            }
        }
    }

    public function __toString() {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

    public function __debugInfo() {
        $properties = [];
        foreach (get_object_vars($this) as $property => $value) {
            if ($value !== UNDEFINED) {
                $properties[$property] = $value;
            }
        }
        return $properties;
    }

    /**
     * Customize the way json_encode() renders the annotations.
     * @return array
     */
    public function jsonSerialize() {
        $data = new stdClass();
        // Strip undefined and null values.
        $classVars = get_class_vars(get_class($this));
        foreach (get_object_vars($this) as $property => $value) {
            if ($value !== UNDEFINED) {
                if ($classVars[$property] === UNDEFINED) { // When default is undefined, null is allowed.
                    $data->$property = $value;
                } elseif ($value !== null) {
                    $data->$property = $value;
                }
            }
        }
        // Strip properties that are for internal (swagger-php) use.
        foreach (static::$_blacklist as $property) {
            unset($data->$property);
        }
        if (static::$_key) {
            $property = static::$_key;
            unset($data->$property);
        }
        // Inject vendor properties.
        unset($data->x);
        if (is_array($this->x)) {
            foreach ($this->x as $property => $value) {
                $prefixed = 'x-' . $property;
                $data->$prefixed = $value;
            }
        }
        // Map keys
        foreach ($data as $property => $value) {
            if (is_array($value)) {
                $array = [];
                foreach ($value as $i => $item) {
                    if ($item instanceof AbstractAnnotation) {
                        $class = get_class($item);
                        if ($class::$_key) {
                            $keyProperty = $class::$_key;
                            $array[$item->$keyProperty] = $item;
                            continue;
                        }
                    }
                    $array[$i] = $item;
                }
                $data->$property = $array;
            }
        }
        // $ref
        if (isset($data->ref)) {
            $dollarRef = '$ref';
            $data->$dollarRef = $data->ref;
            unset($data->ref);
        }
        return $data;
    }

    /**
     * Validate annotation tree, and log notices & warnings.
     * @param array $skip (prevent stackoverflow, when traversing an infinite dependency graph)
     * @return boolean
     */
    public function validate($skip = []) {
        if (in_array($this, $skip, true)) {
            return true;
        }
        $valid = true;
        // Report orphaned annotations
        foreach ($this->_unmerged as $annotation) {
            if (!is_object($annotation)) {
                Logger::notice('Unexpected type: "' . gettype($annotation) . '" in ' . $this->identity() . '->_unmerged, expecting a Annotation object');
                break;
            }
            $class = get_class($annotation);
            if (isset(static::$_nested[$class])) {
                $property = static::$_nested[$class];
                Logger::notice('Multiple ' . $annotation->identity() . ' not allowed for ' . $this->identity() . " in:\n  " . $annotation->_context . "\n  " . $this->$property->_context);
            } elseif ($annotation instanceof AbstractAnnotation) {
                $message = 'Unexpected ' . $annotation->identity();
                if (count($class::$_parents)) {
                    $shortNotations = [];
                    foreach ($class::$_parents as $_class) {
                        $shortNotations[] = '@' . str_replace('Swagger\\Annotations\\', 'SWG\\', $_class);
                    }
                    $message .= ', expected to be inside ' . implode(', ', $shortNotations);
                }
                Logger::notice($message . ' in ' . $annotation->_context);
            }
            $valid = false;
        }
        // Report conflicting key
        foreach ($this as $property => $value) {
            if ($property === '_context') {
                continue;
            }
            if (is_array($value)) {
                $keys = [];
                foreach ($value as $i => $item) {
                    if ($item instanceof AbstractAnnotation) {
                        $class = get_class($item);
                        if ($class::$_key) {
                            $keyProperty = $class::$_key;
                            if (empty($item->$keyProperty)) {
                                Logger::notice($item->identity() . ' is missing key-field: "' . $keyProperty . '" in ' . $item->_context);
                                continue;
                            }
                            if (isset($keys[$item->$keyProperty])) {
                                Logger::notice('Multiple ' . $item->identity() . " with the same header value in:\n  " . $item->_context . "\n  " . $keys[$item->$keyProperty]->_context);
                            }
                            $keys[$item->$keyProperty] = $item;
                            continue;
                        }
                    }
                    $keys[$i] = $item;
                }
            }
        }
        return self::_validate($this, $skip) ? $valid : false;
    }

    /**
     * Recursively validate all annotation properties.
     *
     * @param array|object $fields
     * @param array [$skip] Array with objects which are already validated
     * @return boolean
     */
    private static function _validate($fields, $skip) {
        $valid = true;
        if (is_object($fields)) {
            if (in_array($fields, $skip, true)) {
                return true;
            }
            $skip[] = $fields;
        }
        foreach ($fields as $field => $value) {
            if ($value === null || is_scalar($value) || $field === '_unmerged' || $field === '_context') {
                continue;
            }
            if (is_object($value)) {
                if (method_exists($value, 'validate')) {
                    if (!$value->validate($skip)) {
                        $valid = false;
                    }
                } elseif (!self::_validate($value, $skip)) {
                    $valid = false;
                }
            } elseif (is_array($value) && !self::_validate($value, $skip)) {
                $valid = false;
            }
        }
        return $valid;
    }

    /**
     * Return a identity for easy debugging.
     * Example: "@SWG\Get(path="/pets")"
     * @return string
     */
    public function identity() {
        return '@' . str_replace('Swagger\\Annotations\\', 'SWG\\', get_class($this)) . '()';
    }

}
