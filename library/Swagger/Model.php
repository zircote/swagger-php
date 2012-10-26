<?php

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
 * @category   Swagger
 * @package    Swagger
 * @subpackage Model
 */
namespace Swagger;
use \Exception;
use \Reflector;
use \ReflectionClass;
use \Swagger\AbstractEntity;

/**
 *
 *
 *
 * @category   Swagger
 * @package    Swagger
 * @subpackage Model
 */
class Model extends AbstractEntity
{
    /**
     *
     * @var ReflectionClass
     */
    protected $_class;
    /**
     *
     * @var string
     */
    protected $_docComment;
    /**
     *
     * @var array
     */
    public $results = array();

    /**
     *
     * @param \Reflector|string $class
     * @throws \Exception
     */
    public function __construct($class)
    {
        if (is_object($class) && !$class instanceof \Reflector) {
            $this->_class = new ReflectionClass($class);
        } elseif ($class instanceof Reflector) {
            if (!method_exists($class, 'getDocComment')) {
                throw new Exception('Reflector does not possess a getDocComment method');
            }
            $this->_class = $class;
        } elseif (is_string($class)) {
            $this->_class = new ReflectionClass($class);
        } else {
            throw new \Exception('Incompatable Type attempted to reflect');
        }
        $this
            ->_parseComment()
            ->_getModelId()
            ->_getModelProperties();
    }

    /**
     * @return \Swagger\Model
     */
    protected function _parseComment()
    {
        $this->_docComment = $this->_parseDocComment(
            $this->_class->getDocComment()
        );
        return $this;
    }

    /**
     * @return \Swagger\Model
     */
    protected function _getModelId()
    {
        if (preg_match(self::PATTERN_APIMODEL, $this->_docComment, $matches)) {
            foreach ($this->_parseParts($matches[1]) as $key => $value) {
                $this->results[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * @return \Swagger\Model
     */
    protected function _getModelProperties()
    {
        $this->results['properties'] = array();
        if (preg_match_all(
            self::PATTERN_APIMODELPARAM, $this->_docComment, $matches
        )
        ) {
            foreach ($matches[1] as $match) {
                preg_match('/(\w+)(<.*>|)\s{1,}(\$\w+)(.*)/i', $match, $prop);
                if ($prop) {
                    if (isset($prop[3])) {
                        $name = str_replace('$', '', $prop[3]);
                        if (isset($prop[1])) {
                            $result['type'] = $prop[1];
                        }
                        if (isset($prop[4])) {
                            $result['description'] = trim($prop[4]);
                        }
                        $this->results['properties'][$name] = $result;
                    }
                    if (!empty($prop[2])) {
                        $this->results['properties'][$name] =
                            array_merge(
                                $this->results['properties'][$name],
                                $this->_parseComplexTypes($prop[2])
                            );
                    }
                }
            }
        }
        foreach (
            $this->_class->getProperties(\ReflectionProperty::IS_PUBLIC) as
            $property
        ) {
            $result = $this->_parsePublicProps($property);
            if ($result) {
                $this->results['properties'][$property->getName()] = $result;
            }
        }

        return $this;
    }

    /**
     * @param string $complexType
     * @return array
     */
    protected function _parseComplexTypes($complexType)
    {
        $primitives = array(
            '<boolean>' => array('items' => array('type' => 'boolean')),
            '<bool>'    => array('items' => array('type' => 'boolean')),
            '<int>'     => array('items' => array('type' => 'integer')),
            '<integer>' => array('items' => array('type' => 'integer')),
            '<string>'  => array('items' => array('type' => 'string')),
            '<float>'   => array('items' => array('type' => 'float'))
        );
        if (array_key_exists(strtolower($complexType), $primitives)) {
            return $primitives[strtolower($complexType)];
        }
        if(preg_match('/<ref:(\w+)>/i', $complexType, $match)){
            return array('items' => array('$ref' => $match[1]));
        }
        if(preg_match_all('/([a-zA-Z0-9 ]+)/i', $complexType, $matches)){
            $enum = array('enum' => array());
            foreach ($matches[1] as $match) {
                $match = trim($match);
                if(!empty($match)){
                    $enum['enum'][] = $match;
                }
            }
            return $enum;
        }
        return array();
    }

    /**
     *
     * @param \ReflectionProperty $value
     */
    protected function _parsePublicProps(\ReflectionProperty $property)
    {
        $comment = $this->_parseDocComment($property->getDocComment());
        if(preg_match('/^\w+\s{1,}([^@|)]*)/i', $comment, $match)){
            $result['description'] = trim($match[0]);
            preg_match('/@var (\w+)/i', $comment, $match);
    		if(isset($match[1])){
				$result['type'] = $match[1];
				return $result;
			}
        }
        return false;
    }

    /**
     *
     * @param string $value
     */
    protected function _isRef($value)
    {
        if (preg_match('/$ref:(\w+)$/i', $value, $match)) {
            $value = array('$ref' => $match[1]);
        }
        return $value;
    }
}
