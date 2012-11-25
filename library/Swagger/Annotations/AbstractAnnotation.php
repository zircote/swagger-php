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
use \Doctrine\Common\Annotations\AnnotationException;

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
    const REGEX = '/(:?|\[|\{)\s{0,}\'(:?|\]|\})/';
    const REPLACE = '$1"$2';
    const NEWLINES = '/(?>\r\n|\n|\r|\f|\x0b|\x85|\x{2028}|\x{2029})/u';
    const PREAMBLE = '/^\s+\*\s{0,1}/';

    /**
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        foreach ($values as $k => $v) {
            if (property_exists($this, $k)) {
                $this->{$k} = $v;
            }
        }
    }

    protected function arrayFilter(&$v)
    {
        if(is_string($v) && in_array($v, array('true', 'false'))) {
            $v = ($v == 'true') ? true : false;
        }
        if(empty($v) && $v !== false){
            return false;
        }
        return true;
    }
    /**
     * @return array
     */
    public function toArray()
    {
        $members =  array_filter((array) $this, array($this, 'arrayFilter'));
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
        return $members;
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
        $string = preg_replace(self::NEWLINES, PHP_EOL, $string);
        $values = explode(PHP_EOL, $string);
        foreach ($values as $key => $value) {
            $values[$key] = preg_replace(self::PREAMBLE, null, $value);
        }
        return implode(PHP_EOL, $values);
    }
}

