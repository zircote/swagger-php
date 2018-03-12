<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

use Exception;
use JsonSerializable;
use stdClass;
use Swagger\Analyser;
use Swagger\Context;
use Swagger\Logger;

/**
 * The openapi annotation base class.
 */
abstract class AbstractAnnotation implements JsonSerializable
{
    /**
     * While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.
     * For further details see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#specificationExtensions
     * The keys inside the array will be prefixed with `x-`.
     *
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
     * The properties which are required by [the spec](https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md)
     * @var array
     */
    public static $_required = [];

    /**
     * Specify the type of the property.
     * Examples:
     *   'name' => 'string' // a string
     *   'required' => 'boolean', // true or false
     *   'tags' => '[string]', // array containing strings
     *   'in' => ["query", "header", "path", "formData", "body"] // must be one on these
     * @var array
     */
    public static $_types = [];

    /**
     * Declarative mapping of Annotation types to properties.
     * Examples:
     *   'Swagger\Annotation\Info' => 'info', // Set @OAS\Info annotation as the info property.
     *   'Swagger\Annotation\Parameter' => ['parameters'],  // Append @OAS\Parameter annotations the parameters array.
     *   'Swagger\Annotation\PathItem' => ['paths', 'path'],  // Append @OAS\PathItem annotations the paths array and use path as key.
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
     * @param array $properties
     */
    public function __construct($properties)
    {
        if (isset($properties['_context'])) {
            $this->_context = $properties['_context'];
            unset($properties['_context']);
        } elseif (Analyser::$context) {
            $this->_context = Analyser::$context;
        } else {
            $this->_context = Context::detect(1);
        }
        if ($this->_context->is('annotations') === false) {
            $this->_context->annotations = [];
        }
        $this->_context->annotations[] = $this;
        $nestedContext = new Context(['nested' => $this], $this->_context);
        foreach ($properties as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
                if (is_array($value)) {
                    foreach ($value as $key => $annotation) {
                        if (is_object($annotation) && $annotation instanceof AbstractAnnotation) {
                            $this->{$property}[$key] = $this->nested($annotation, $nestedContext);
                        }
                    }
                }
            } elseif ($property !== 'value') {
                $this->$property = $value;
            } elseif (is_array($value)) {
                $annotations = [];
                foreach ($value as $annotation) {
                    if (is_object($annotation) && $annotation instanceof AbstractAnnotation) {
                        $annotations[] = $annotation;
                    } else {
                        Logger::notice('Unexpected field in ' . $this->identity() . ' in ' . $this->_context);
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

    public function __get($property)
    {
        $properties = get_object_vars($this);
        Logger::notice('Property "' . $property . '" doesn\'t exist in a ' . $this->identity() . ', existing properties: "' . implode('", "', array_keys($properties)) . '" in ' . $this->_context);
    }

    public function __set($property, $value)
    {
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
     * @param bool $ignore Ignore unmerged annotations
     * @return AbstractAnnotation[] The unmerged annotations
     */
    public function merge($annotations, $ignore = false)
    {
        $unmerged = [];
        $nestedContext = new Context(['nested' => $this], $this->_context);
        foreach ($annotations as $annotation) {
            $found = false;
            foreach (static::$_nested as $class => $property) {
                if ($annotation instanceof $class) {
                    if (is_array($property)) { // Append to an array?
                        $property = $property[0];
                        if ($this->$property === null) {
                            $this->$property = [];
                        }
                        array_push($this->$property, $this->nested($annotation, $nestedContext));
                        $found = true;
                    } elseif ($this->$property === null) {
                        $this->$property = $this->nested($annotation, $nestedContext);
                        $found = true;
                    }
                    break;
                }
            }
            if ($found === false) {
                $unmerged[] = $annotation;
            }
        }
        if (!$ignore) {
            foreach ($unmerged as $annotation) {
                $this->_unmerged[] = $this->nested($annotation, $nestedContext);
            }
        }
        return $unmerged;
    }

    /**
     * Merge the properties from the given object into this annotation.
     * Prevents overwriting properties that are already configured.
     *
     * @param object $object
     */
    public function mergeProperties($object)
    {
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
                $identity = method_exists($object, 'identity') ? $object->identity() : get_class($object);
                $context1 = $this->_context;
                $context2 = property_exists($object, '_context') ? $object->_context : 'unknown';
                if (is_object($this->$property) && $this->$property instanceof AbstractAnnotation) {
                    $context1 = $this->$property->_context;
                }
                Logger::warning('Multiple definitions for ' . $identity . '->' . $property . "\n     Using: " . $context1 . "\n  Skipping: " . $context2);
            }
        }
    }

    public function __toString()
    {
        return json_encode($this, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function __debugInfo()
    {
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
     */
    public function jsonSerialize()
    {
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
        // Correct empty array to empty objects.
        foreach (static::$_types as $property => $type) {
            if ($type === 'object' && is_array($data->$property) && empty($data->$property)) {
                $data->$property = new stdClass;
            }
        }
        // Inject vendor properties.
        unset($data->x);
        if (is_array($this->x)) {
            foreach ($this->x as $property => $value) {
                $prefixed = 'x-' . $property;
                $data->$prefixed = $value;
            }
        }
        // Map nested keys
        foreach (static::$_nested as $nested) {
            if (is_string($nested) || count($nested) === 1) {
                continue;
            }
            $property = $nested[0];
            if ($this->$property === null) {
                continue;
            }
            $keyField = $nested[1];
            $object = new stdClass();
            foreach ($this->$property as $key => $item) {
                if (is_numeric($key) === false && is_array($item)) {
                    $object->$key = $item;
                } else {
                    $key = $item->$keyField;
                    if ($key && empty($object->$key)) {
                        if (method_exists($item, 'jsonSerialize')) {
                            $object->$key = $item->jsonSerialize();
                        } else {
                            $object->$key = $item;
                        }
                        unset($object->$key->$keyField);
                    }
                }
            }
            $data->$property = $object;
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
     * @param array $parents The path of annotations above this annotation in the tree.
     * @param array $skip (prevent stack overflow, when traversing an infinite dependency graph)
     * @return boolean
     * @throws Exception
     */
    public function validate($parents = [], $skip = [], $ref = '')
    {
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
                Logger::notice('Only one @' . str_replace('Swagger\Annotations\\', 'OAS\\', get_class($annotation)) . '() allowed for ' . $this->identity() . " multiple found in:\n    Using: " . $this->$property->_context . "\n  Skipped: " . $annotation->_context);
            } elseif ($annotation instanceof AbstractAnnotation) {
                $message = 'Unexpected ' . $annotation->identity();
                if (count($class::$_parents)) {
                    $shortNotations = [];
                    foreach ($class::$_parents as $_class) {
                        $shortNotations[] = '@' . str_replace('Swagger\Annotations\\', 'OAS\\', $_class);
                    }
                    $message .= ', expected to be inside ' . implode(', ', $shortNotations);
                }
                Logger::notice($message . ' in ' . $annotation->_context);
            }
            $valid = false;
        }
        // Report conflicting key

        foreach (static::$_nested as $annotationClass => $nested) {
            if (is_string($nested) || count($nested) === 1) {
                continue;
            }
            $property = $nested[0];
            if ($this->$property === null) {
                continue;
            }
            $keys = [];
            $keyField = $nested[1];
            foreach ($this->$property as $key => $item) {
                if (is_array($item) && is_numeric($key) === false) {
                    Logger::notice($this->identity() . '->' . $property . ' is an object literal, use nested @' . str_replace('Swagger\\Annotations\\', 'OAS\\', $annotationClass) . '() annotation(s) in ' . $this->_context);
                    $keys[$key] = $item;
                } elseif (empty($item->$keyField)) {
                    Logger::notice($item->identity() . ' is missing key-field: "' . $keyField . '" in ' . $item->_context);
                } elseif (isset($keys[$item->$keyField])) {
                    Logger::notice('Multiple ' . $item->_identity([]) . ' with the same ' . $keyField . '="' . $item->$keyField . "\":\n  " . $item->_context . "\n  " . $keys[$item->$keyField]->_context);
                } else {
                    $keys[$item->$keyField] = $item;
                }
            }
        }
        if (isset($this->ref)) {
            if (substr($this->ref, 0, 2) === '#/' && count($parents) > 0  && $parents[0] instanceof Swagger) { // Internal reference
                try {
                    $parents[0]->ref($this->ref);
                } catch (Exception $exception) {
                    Logger::notice($exception->getMessage().' for '.$this->identity().' in '.$this->_context);
                }
            }
        } else {
            // Report missing required fields (when not a $ref)
            foreach (static::$_required as $property) {
                if ($this->$property === null || $this->$property === UNDEFINED) {
                    $message = 'Missing required field "' . $property . '" for ' . $this->identity() . ' in ' . $this->_context;
                    foreach (static::$_nested as $class => $nested) {
                        $nestedProperty = is_array($nested) ? $nested[0] : $nested;
                        if ($property === $nestedProperty) {
                            if ($this instanceof OpenApi) {
                                $message = 'Required @' . str_replace('Swagger\\Annotations\\', 'OAS\\', $class) . '() not found';
                            } elseif (is_array($nested)) {
                                $message = $this->identity() . ' requires at least one @' . str_replace('Swagger\\Annotations\\', 'OAS\\', $class) . '() in ' . $this->_context;
                            } else {
                                $message = $this->identity() . ' requires a @' . str_replace('Swagger\\Annotations\\', 'OAS\\', $class) . '() in ' . $this->_context;
                            }
                            break;
                        }
                    }
                    Logger::notice($message);
                }
            }
        }
        // Report invalid types
        foreach (static::$_types as $property => $type) {
            $value = $this->$property;
            if ($value === null || $value === UNDEFINED) {
                continue;
            }
            if (is_string($type)) {
                if ($this->validateType($type, $value) === false) {
                    $valid = false;
                    Logger::notice($this->identity() . '->' . $property . ' is a "' . gettype($value) . '", expecting a "' . $type . '" in ' . $this->_context);
                }
            } elseif (is_array($type)) { // enum?
                if (in_array($value, $type) === false) {
                    Logger::notice($this->identity() . '->' . $property . ' "' . $value . '" is invalid, expecting "' . implode('", "', $type) . '" in ' . $this->_context);
                }
            } else {
                throw new Exception('Invalid ' . get_class($this) . '::$_types[' . $property . ']');
            }
        }
        $parents[] = $this;
        return self::_validate($this, $parents, $skip, $ref) ? $valid : false;
    }

    /**
     * Recursively validate all annotation properties.
     *
     * @param array|object $fields
     * @param array $parents The path of annotations above this annotation in the tree.
     * @param array [$skip] Array with objects which are already validated
     * @return boolean
     */
    private static function _validate($fields, $parents, $skip, $baseRef)
    {
        $valid = true;
        $blacklist = [];
        if (is_object($fields)) {
            if (in_array($fields, $skip, true)) {
                return true;
            }
            $skip[] = $fields;
            $blacklist = property_exists($fields, '_blacklist') ? $fields::$_blacklist : [];
        }

        foreach ($fields as $field => $value) {
            if ($value === null || is_scalar($value) || in_array($field, $blacklist)) {
                continue;
            }
            $ref = $baseRef !== '' ? $baseRef.'/'.urlencode((string)$field) : urlencode((string)$field);
            if (is_object($value)) {
                if (method_exists($value, 'validate')) {
                    if (!$value->validate($parents, $skip, $ref)) {
                        $valid = false;
                    }
                } elseif (!self::_validate($value, $parents, $skip, $ref)) {
                    $valid = false;
                }
            } elseif (is_array($value) && !self::_validate($value, $parents, $skip, $ref)) {
                $valid = false;
            }
        }
        return $valid;
    }

    /**
     * Return a identity for easy debugging.
     * Example: "@OAS\Get(path="/pets")"
     * @return string
     */
    public function identity()
    {
        return $this->_identity([]);
    }

    /**
     * Helper for generating the identity()
     * @param array $properties
     * @return string
     */
    protected function _identity($properties)
    {
        $fields = [];
        foreach ($properties as $property) {
            $value = $this->$property;
            if ($value !== null && $value !== UNDEFINED) {
                $fields[] = $property . '=' . (is_string($value) ? '"' . $value . '"' : $value);
            }
        }
        return '@' . str_replace('Swagger\\Annotations\\', 'OAS\\', get_class($this)) . '(' . implode(',', $fields) . ')';
    }

    private function validateType($type, $value)
    {
        if (substr($type, 0, 1) === '[' && substr($type, -1) === ']') { // Array of a specified type?
            if ($this->validateType('array', $value) === false) {
                return false;
            }
            $itemType = substr($type, 1, -1);
            foreach ($value as $i => $item) {
                if ($this->validateType($itemType, $item) === false) {
                    return false;
                }
            }
            return true;
        }
        switch ($type) {
            case 'string':
                return is_string($value);

            case 'boolean':
                return is_bool($value);

            case 'integer':
                return is_int($value);

            case 'number':
                return is_numeric($value);

            case 'array':
                if (is_array($value) === false) {
                    return false;
                }
                $count = 0;
                foreach ($value as $i => $item) {
                    if ($count !== $i) { // not a array, but a hash/map
                        return false;
                    }
                    $count++;
                }
                return true;

            case 'scheme':
                return in_array($value, ['http', 'https', 'ws', 'wss']);

            default:
                throw new Exception('Invalid type "' . $type . '"');
        }
    }

    /**
     * Wrap the context with a reference to the annotation it is nested in.
     * @param AbstractAnnotation $annotation
     * @param Context $nestedContext
     * @return AbstractAnnotation
     */
    private function nested($annotation, $nestedContext)
    {
        if (property_exists($annotation, '_context') && $annotation->_context === $this->_context) {
            $annotation->_context = $nestedContext;
        }
        return $annotation;
    }
}
