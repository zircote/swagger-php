<?php
namespace Swagger\Annotations;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2012] [Robert Allen]
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
use Swagger\Logger;

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
     * Allows Annotation classes to know which property or method in which class is being processed.
     * @var string
     */
    public static $context = '';

    const REGEX = '/(:?|\[|\{)\s{0,}\'(:?|\]|\})/';
    const REPLACE = '$1"$2';
    const NEWLINES = '/(?>\r\n|\n|\r|\f|\x0b|\x85|\x{2028}|\x{2029})/u';
    const PREAMBLE = '/^\s+\*\s{0,1}/';

    /**
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        foreach ($values as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $this->cast($value);
            } elseif ($key !== 'value') {
                $properties = array_keys(get_object_vars($this));
                Logger::notice('Skipping unsupported property: "'.$key.'" for @'.get_class($this).', expecting "'.implode('", "', $properties).'" in '.AbstractAnnotation::$context);
            }
        }
        if (isset($values['value'])) {
            $nested = is_array($values['value']) ? $values['value'] : array($values['value']);
            $objects = array();
            foreach ($nested as $value) {
                if (is_object($value)) {
                    $objects[] = $value;
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
     * @param type $annotations
     */
    protected function setNestedAnnotations($annotations)
    {
        foreach ($annotations as $annotation) {
            Logger::notice('Unexpected '.get_class($annotation).' in a '.get_class($this).' in '.AbstractAnnotation::$context);
        }
    }

    /**
     * Example: @Annotation("hello", 124) would call setNestedValues with array("hello", 123)
     * @param array $values
     */
    protected function setNestedValue($value)
    {
        Logger::notice('Unexpected value "'.$value.'", direct values not supported for '.get_class($this).' in '.AbstractAnnotation::$context);
    }

    private function cast($value)
    {
        if (is_string($value) && in_array($value, array('true', 'false'))) {
            return ($value == 'true') ? true : false;
        }
        return $value;
    }

    protected function arrayFilter(&$v)
    {
        if (is_string($v) && in_array($v, array('true', 'false'))) {
            $v = ($v == 'true') ? true : false;
        }
        if (empty($v) && $v !== false) {
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $members = array_filter((array) $this, array($this, 'arrayFilter'));
        $result = array();
        foreach ($members as $k => $m) {
            if ($m instanceof AbstractAnnotation) {
                $members[$k] = $m->toArray();
            }
        }
        if (isset($members['value'])) {
            foreach ($members['value'] as $k => $m) {
                if ($m instanceof AbstractAnnotation) {
                    $result[] = $m->toArray();
                }
            }
            if ($members['value'] instanceof AbstractAnnotation) {
                $result[] = $members['value']->toArray();
            }
        }
        if (isset($this->reflector) && !$this->reflector instanceof \ReflectionProperty) {
            unset($this->reflecor);
        }
        return $members;
    }

    public function jsonSerialize()
    {
        $data = get_object_vars($this);
        foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]); // Skip undefined values
            }
        }
        return $data;
    }

    /**
     * @param $json
     * @throws \Doctrine\Common\Annotations\AnnotationException
     *
     * @return mixed
     */
    public function decode($json)
    {
        $json = preg_replace(self::REGEX, self::REPLACE, $json);
        $json = json_decode($json);
        if ($error = json_last_error()) {
            throw new AnnotationException(sprintf('json decode error [%s]', $error));
        }
        return $json;
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
}
