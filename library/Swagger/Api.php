<?php
namespace Swagger;
use \Swagger\AbstractEntity;
use \Swagger\Operation;
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
 * @category   Swagger
 * @package    Swagger
 * @subpackage Api
 */
/**
 *
 *
 *
 * @category   Swagger
 * @package    Swagger
 * @subpackage Api
 */
class Api extends AbstractEntity
{
    /**
     *
     * @var \ReflectionClass
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
    public $results = array(
        'operations' => array()
    );
    /**
     *
     * @param \Reflector|string $class
     * @throws \Exception
     */
    public function __construct($class)
    {
        if(is_object($class) && !$class instanceof \Reflector){
            $this->_class = new \ReflectionClass($class);
        } elseif($class instanceof \Reflector){
            if(!method_exists($class, 'getDocComment')){
                throw new \Exception('Reflector does not possess a getDocComment method');
            }
            $this->_class = $class;
        } elseif(is_string($class)){
            $this->_class = new \ReflectionClass($class);
        } else {
            throw new \Exception('Incompatable Type attempted to reflect');
        }
        $this->_parseApi()
            ->_getResource()
            ->_getApi()
            ->_getProduces()
            ->_getMethods();
    }
    /**
     * @return \Swagger\Api
     */
    protected function _parseApi()
    {
        $this->_docComment = $this->_parseDocComment(
            $this->_class->getDocComment()
        );
        return $this;
    }
    /**
     * @return \Swagger\Api
     */
    protected function _getResource()
    {
        $comment = preg_replace(self::STRIP_LINE_PREAMBLE, null, $this->_docComment);
        if(preg_match(self::PATTERN_RESOURCE,  $comment, $matches)){
            foreach ($this->_parseParts($matches[1]) as $key => $value) {
                $this->results[$key] = $value;
            }

        }
        return $this;
    }
    /**
     * @return \Swagger\Api
     */
    protected function _getApi()
    {
        $comment = preg_replace(self::STRIP_LINE_PREAMBLE, null, $this->_docComment);
        if(preg_match(self::PATTERN_API,  $comment, $matches)){
            $this->results = array_merge_recursive($this->results, $this->_parseParts($matches[1]));
            $this->results['path'] = $this->results['path'];
        }
        return $this;
    }
    /**
     * @return \Swagger\Api
     */
    protected function _getProduces()
    {
        $comment = preg_replace(self::STRIP_LINE_PREAMBLE, null, $this->_docComment);
        if(preg_match(self::PATTERN_PRODUCES,  $comment, $matches)){
            foreach (explode(',', $matches[1]) as $value) {
                $result[] = preg_replace(self::STRIP_WHITESPACE_APOST,null,$value);
            }
            $this->results['produces'] = $result;
        }
        return $this;
    }
    /**
     * @return \Swagger\Api
     */
    protected function _getMethods()
    {
        /* @var $reflectedMethod ReflectionMethod */
        foreach ($this->_class->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectedMethod) {
            if(preg_match('/@ApiOperation/i', $reflectedMethod->getDocComment())){
                $operation = new Operation($reflectedMethod, $this->results);
                array_push($this->results['operations'],$operation->results);
            }
        }
        return $this;
    }
}