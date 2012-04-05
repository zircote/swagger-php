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
 * @subpackage UnitTests
 */
require_once 'Swagger.php';
require_once 'Swagger/Resource.php';
/**
 *
 *
 *
 * @category   Swagger
 * @package    Swagger
 * @subpackage UnitTests
 */
class Swagger_ResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Swagger_Resource
     */
    protected $resource;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Swagger_Resource::buildResource
     * @todo   Implement testBuildResource().
     */
    public function testBuildResource()
    {
        $path = realpath(dirname(dirname(dirname(__DIR__))) . '/sample');
        $swagger = Swagger::discover($path);

        echo $swagger->getResource('http://org.local/v1');

        echo PHP_EOL,PHP_EOL;

        echo $swagger->getApi('http://org.local/v1', '/leadresponder');
//         print_r($swagger->models->results);
    }

}
