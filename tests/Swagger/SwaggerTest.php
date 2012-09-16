<?php
namespace SwaggerTests;

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
 * @category   SwaggerTests
 * @package    Swagger
 * @subpackage UnitTests
 */
use Swagger\Swagger;

/*
 *
 *
 * @category   SwaggerTests
 * @package    Swagger
 * @subpackage UnitTests
 * @group Swagger
 */
class SwaggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Swagger\Resource
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
     * @group Resource
     */
    public function testBuildResource()
    {
        $path = __DIR__ . '/Fixtures';
        $swagger = Swagger::discover($path);
        echo $swagger->jsonEncode($swagger->registry, true);
    }
}

