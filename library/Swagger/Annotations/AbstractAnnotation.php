<?php
namespace Swagger\Annotations;

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
 *
 * @package
 * @category
 * @subpackage
 */

use Doctrine\Common\Annotations\AnnotationException;
use Swagger\Annotations\AbstractAnnotation;
use Swagger\Annotations\Partial;
use Swagger\Context;
use Swagger\Logger;
use Swagger\Parser;

/**
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 * @Target({"ALL"})
 */
abstract class AbstractAnnotation
{
    /**
     * This annotiation is a partial id, to be used in conjunction with @SWG\Partial()
     * @var string|null
     */
    public $_partialId;

    /**
     * The partials that must be applied to this annotation.
     * @var array
     */
    public $_partials = array();

    /**
     * @var Context
     */
    public $_context;

    /**
     * Declarative mapping of Annotation types to properties
     * @var array
     */
    protected static $mapAnnotations = array();

    const REGEX = '/(:?|\[|\{)\s{0,}\'(:?|\]|\})/';
    const REPLACE = '$1"$2';
    const NEWLINES = '/(?>\r\n|\n|\r|\f|\x0b|\x85|\x{2028}|\x{2029})/u';
    const PREAMBLE = '/^\s+\*\s{0,1}/';

    /**
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        $this->_context = Parser::$context;
        if ($this->_context === null) {
            $this->_context = new Context();
        }
        foreach ($values as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            } elseif ($key === 'partial') {
                $this->_partialId = $value;
            } elseif ($key !== 'value') {
                $properties = array_keys(get_object_vars($this));
                Logger::notice('Skipping unsupported property: "'.$key.'" for @'.get_class($this).', expecting "'.implode('", "', $properties).'" in '.$this->_context);
            }
        }
        if (isset($values['value'])) {
            $nested = is_array($values['value']) ? $values['value'] : array($values['value']);
            $objects = array();
            foreach ($nested as $value) {
                if (is_object($value)) {
                    if ($value instanceof Partial) {
                        $this->_partials[] = $value->use;
                    } else {
                        $objects[] = $value;
                    }
                } else {
                    $this->setNestedValue($value);
                }
            }
            if (count($objects)) {
                $this->setNestedAnnotations($objects);
            }
        }
    }

    /**
     * @return boolean
     */
    public function hasPartialId()
    {
        return $this->_partialId !== null;
    }

    /**
     * Log warning, correct errors where possible.
     * @return bool Return false when the annotation is invalid and can't be used.
     */
    public function validate()
    {
        Logger::warning(get_class($this).' doesn\'t implement the validate() method');
        return false;
    }

    /**
     * Example: @Annotation(@Nested) would call setNestedAnnotations with array(Nested)
     *
     * @param AbstractAnnotation[] $annotations
     */
    public function setNestedAnnotations($annotations)
    {
        $map = static::$mapAnnotations;
        $map['\Swagger\Annotations\Partial'] = '_partials[]';

        foreach ($annotations as $annotation) {
            $found = false;
            foreach ($map as $class => $property) {
                if ($annotation instanceof $class) {
                    if (substr($property, -2) === '[]') { // Append to array?
                        $property = substr($property, 0, -2);
                        if ($this->$property === null) {
                            $this->$property = array();
                        }
                        array_push($this->$property, $annotation);
                    } else {
                        $this->$property = $annotation;
                    }
                    $found = true;
                    break;
                }
            }
            if ($found === false) {
                Logger::notice('Unexpected '.get_class($annotation).' in a '.get_class($this).' in '.$this->_context.' expecting '.implode(', ', array_keys($map)));
            }
        }
    }

    /**
     * Example: @Annotation("hello", 124) would call setNestedValues with array("hello", 123)
     * @param array $values
     */
    protected function setNestedValue($value)
    {
        Logger::notice('Unexpected value "'.$value.'", direct values not supported for '.get_class($this).' in '.$this->_context);
    }

    public function jsonSerialize()
    {
        $data = get_object_vars($this);
        unset($data['_partialId'], $data['_partials'], $data['_context']);
        foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]); // Skip undefined values
            }
        }
        return $data;
    }

    /**
     * @param string $json
     * @throws AnnotationException     */
    public static function decode($json)
    {
        $json = preg_replace(self::REGEX, self::REPLACE, $json);
        $data = json_decode($json);
        $error = json_last_error();
        if ($error) {
            throw new AnnotationException(sprintf('json decode error [%s]', $error));
        }
        return $data;
    }

    /**
     * @param $string
     *
     * @return string
     */
    public function removePreamble($string)
    {
        if ($string === null) {
            return null;
        }
        $string = preg_replace(self::NEWLINES, PHP_EOL, $string);
        $values = explode(PHP_EOL, $string);
        foreach ($values as $key => $value) {
            $values[$key] = preg_replace(self::PREAMBLE, null, $value);
        }
        return implode(PHP_EOL, $values);
    }

    /**
     * Return a identity for easy debugging.
     * Example: "SWG\Model(id="Pet")"
     * @return string
     */
    public function identity()
    {
        $array = explode('\\', get_class($this));
        return '@SWG\\'.array_pop($array).'()';
    }
}
