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
 * @category   Swagger
 * @package    Swagger
 * @subpackage Resource
 */
require_once 'Swagger/Model.php';
/**
 *
 *
 *
 * @category   Swagger
 * @package    Swagger
 * @subpackage Resource
 */
class Swagger_Models extends Swagger_AbstractEntity
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
     * @return Swagger_Models
     */
    protected function _introSpec()
    {
        foreach ($this->_classList as $reflectedClass) {
            $ref = new Swagger_Model($reflectedClass);
            if(isset($ref->results['id'])){
                $this->results[$ref->results['id']] = $ref->results;
            }
        }
        return $this;
    }
}