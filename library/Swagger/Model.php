<?php

/**
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * Copyright [2012] [Robert Allen]
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
 * @category Swagger
 * @package Swagger
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
 * @category Swagger
 * @package Swagger
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
        if(is_object($class) && !$class instanceof \Reflector){
            $this->_class = new ReflectionClass($class);
        } elseif($class instanceof Reflector){
            if(!method_exists($class, 'getDocComment')){
                throw new Exception('Reflector does not possess a getDocComment method');
            }
            $this->_class = $class;
        } elseif(is_string($class)){
            $this->_class = new ReflectionClass($class);
        } else {
            throw new \Exception('Incompatable Type attempted to reflect');
        }
        $this->_parseComment()->_getModelId()->_getModelProperties();
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
        if(preg_match(self::PATTERN_APIMODEL, $this->_docComment, $matches)){
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
        if(preg_match_all(self::PATTERN_APIMODELPARAM, $this->_docComment, $matches)){
            foreach ($matches[1] as $match) {
                $prop = array();
                foreach ($this->_parseParts($match) as $key => $value) {
                    $prop[$key] = $value;
                }
                array_push($this->results['properties'], $prop);
            }
        }
        return $this;
    }
    /**
     *
     * @param string $value
     */
    protected function _parseType($value)
    {

    }
    /**
     *
     * @param string $value
     */
    protected function _isRef($value)
    {
        if(preg_match('/$ref:(\w+)$/i', $value, $match)){
            $value = array('$ref' => $match[1]);
        }
        return $value;
    }
}
