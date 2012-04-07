<?php
namespace Swagger;
use \Swagger\AbstractEntity;
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
 * @subpackage Param
 */
// use \Swagger\AbstractEntity;
/**
 *
 *
 *
 * @category Swagger
 * @package Swagger
 * @subpackage Param
 */
class Param extends AbstractEntity
{
    /**
     *
     * @var array
     */
    public $results = array();
    /**
     *
     * @var string
     */
    protected $_rawComment;
    /**
     *
     * @param string $apiParam
     */
    public function __construct($apiParam)
    {
        $this->_rawComment = $apiParam;
        $this->results = $this->_parseParts($this->_rawComment);
    }
}