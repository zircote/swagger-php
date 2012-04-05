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
 * @category   Swagger
 * @package    Swagger
 * @subpackage Resource
 */
require_once 'Swagger/Api.php';
/**
 *
 *
 *
 * @category   Swagger
 * @package    Swagger
 * @subpackage Resource
 */
class Swagger_Resource extends Swagger_AbstractEntity
{
    public $results = array();
    protected $_classList;
    /**
     *
     * @param Reflector $path
     */
    public function __construct($classList)
    {
        $this->_classList = $classList;
        $this->_introSpec();
    }
    /**
     *
     * @return Swagger_Resource
     */
    protected function _introSpec()
    {
        foreach ($this->_classList as $reflectedClass) {
            $res = new Swagger_Api($reflectedClass);
            if(isset($res->results['basePath'])){
                $this->results[$res->results['basePath']][$res->results['path']] = $res->results;
            }
        }
        return $this;
    }
    /**
     *
     * @return multitype:
     */
    public function getResources()
    {
        return array_keys($this->results);
    }
    public function getResource($basePath)
    {
        if(!isset($this->results[$basePath])){
            throw new Exception(sprintf('Resource [%s] is not found',$basePath));
        }
        return $this->results[$basePath];
    }
}