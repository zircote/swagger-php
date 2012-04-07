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
 * @subpackage Operation
 */
namespace Swagger;
use \Exception;
use \Reflector;
use \ReflectionClass;
use \Swagger\AbstractEntity;
use \Swagger\Param;
/**
 *
 *
 *
 * @category Swagger
 * @package Swagger
 * @subpackage Operation
 */
class Operation extends AbstractEntity
{
    /**
     *
     * @var \ReflectionMethod
     */
    protected $_operation;
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
        'tags' => array(),
        'errorResponses' => array(),
        'parameters' => array()
    );
    /**
     *
     * @param \Reflector|string $operation
     * @throws \Exception
     */
    public function __construct($operation, $resource)
    {
        $this->_resource = $resource;
        if($operation instanceof Reflector){
            if(!method_exists($operation, 'getDocComment')){
                throw new Exception('Reflector does not possess a getDocComment method');
            }
            $this->_operation = $operation;
        } elseif(is_string($operation)){
            $this->_operation = new ReflectionClass($operation);
        } else {
            throw new Exception('Incompatable Type attempted to reflect');
        }
        $this->_parse();
    }
    /**
     *
     */
    protected function _parse()
    {
        $this->_docComment = $this->_parseDocComment(
            $this->_operation->getDocComment()
        );
        $this->_getMethod()
            ->_getPath()
            ->_getOperation()
            ->_getApiError()
            ->_getParam();
    }
    /**
     * @return \Swagger\Operation
     */
    protected function _getMethod()
    {
        if(preg_match(self::PATTERN_METHOD, $this->_docComment, $match)){
            $this->results['httpMethod'] = str_replace('@', null, $match[1]);
        }
        return $this;
    }
    /**
     * @return \Swagger\Operation
     */
    protected function _getPath()
    {
        $path = null;
        if(preg_match(self::PATTERN_PATH, $this->_docComment, $matches)){
            $path = $matches[1];
            $this->results['path'] = $path;
        }
        return $this;
    }
    /**
     * @return \Swagger\Operation
     */
    protected function _getOperation()
    {
        if(preg_match_all(self::PATTERN_OPERATION,  $this->_docComment, $matches)){
            foreach ($matches[1] as $match) {
                $this->results = array_merge_recursive($this->results, $this->_parseParts($match));
            }
            if(isset($this->results['multiValueResponse']) &&
                strtolower($this->results['multiValueResponse']) == 'true'){
                $this->results['responseClass'] = 'List['.$this->results['responseClass'].']';
            }
            $this->results['summary'] = $this->results['value'];
            unset($this->results['value'],$this->results['multiValueResponse']);
        }
        return $this;
    }
    /**
     * @return \Swagger\Operation
     */
    protected function _getApiError()
    {
        if(preg_match_all(self::PATTERN_APIERROR,  $this->_docComment, $matches)){
            foreach ($matches[1] as $match) {
                array_push($this->results['errorResponses'],$this->_parseParts($match));
            }
        }
        return $this;
    }
    /**
     * @return \Swagger\Operation
     */
    protected function _getParam()
    {
        if(preg_match_all(self::PATTERN_APIPARAM, $this->_docComment, $matches)){
            foreach ($matches[1] as $match) {
                $apiOperation = new Param($match);
                array_push($this->results['parameters'],$apiOperation->results);
            }
        }
        return $this;
    }
}