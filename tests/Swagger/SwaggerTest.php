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
     * @param $expectedArray
     * @param $json
     */
    public function assertJsonEqualToExpectedArray($expectedArray, $json)
    {
        $json = json_decode($json, true);
        $this->assertEquals($expectedArray, $json);
    }
    /**
     * @group Resource
     */
    public function testBuildResource()
    {
        $path = __DIR__ . '/Fixtures';
        $swagger = Swagger::discover($path);
        $expected = json_decode(file_get_contents(__DIR__ . '/Fixtures/pet.json'), true);
        $this->assertEquals($expected, $swagger->registry['/pet']);
    }

    public function testRangeValueType()
    {
        $path = __DIR__ . '/Fixtures';
        $swagger = Swagger::discover($path);
        $expected = json_decode(file_get_contents(__DIR__ . '/Fixtures/sidekick.json'), true);
        $this->assertEquals($expected, $swagger->models['Sidekick']);
    }

    public function testSerializeUnserialize()
    {
        $path = __DIR__ . '/Fixtures';
        $swagger = Swagger::discover($path);
        $serialized = $swagger->serialize();
        $newSwagger = new Swagger();
        $newSwagger->unserialize($serialized);
        $this->assertEquals($swagger->models, $newSwagger->models);
        $this->assertEquals($swagger->registry, $newSwagger->registry);
        $expected = json_decode(file_get_contents(__DIR__ . '/Fixtures/user.json'), true);
        $actual =  $swagger->registry['/user'];
        $this->assertEquals($expected, $actual);
    }

    public function testStore()
    {
        $path = __DIR__ . '/Fixtures';
        $swagger = Swagger::discover($path);
        $expected = json_decode(file_get_contents(__DIR__ . '/Fixtures/store.json'), true);
        $actual =  $swagger->registry['/store'];
        $this->assertEquals($expected, $actual);
    }
    public function testCliTool()
    {
        $path = __DIR__ . '/Fixtures';
        $swagger = Swagger::discover($path);
        $output = sys_get_temp_dir();
        $pathToCli = dirname(dirname(__DIR__)) . '/bin/swagger';
        `$pathToCli -o $output -p $path`;
        foreach (array('user','pet','store') as $record) {
            $json = file_get_contents($output . "/$record.json");
            $this->assertJsonEqualToExpectedArray($swagger->registry["/{$record}"], $json);
        }
    }
    public function testCaching()
    {
        $path = __DIR__ . '/Fixtures';
        $swagger = Swagger::discover($path);
        $cacheKey = sha1($path);
        $this->assertTrue($swagger->getCache()->contains($cacheKey));
        $swag1 = new Swagger();
        $swag1->unserialize($swagger->getCache()->fetch($cacheKey));
        $this->assertEquals($swagger->models, $swag1->models);
        $this->assertEquals($swagger->registry['/user'], $swag1->registry['/user']);
    }

    /**
     * @group listing
     */
    public function testResourcelist()
    {
        
        $path = __DIR__ . '/Fixtures';
        $swagger = Swagger::discover($path);
        $this->assertEquals(
            json_decode(file_get_contents($path . '/api-docs.json'), true),
            json_decode($swagger->getResourceList(), true)
        );
    }
}

