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

/**
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
		$this->assertEquals($expected, Swagger::export($swagger->registry['/pet']));
    }

    public function testRangeValueType()
    {
        $path = __DIR__ . '/Fixtures';
        $swagger = Swagger::discover($path);
        $expected = json_decode(file_get_contents(__DIR__ . '/Fixtures/sidekick.json'), true);
        $this->assertEquals($expected, Swagger::export($swagger->models['Sidekick']));
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
        $actual = Swagger::export($swagger->registry['/user']);
        $this->assertEquals($expected, $actual);
    }

    public function testStore()
    {
        $path = __DIR__ . '/Fixtures';
        $swagger = Swagger::discover($path);
        $expected = json_decode(file_get_contents(__DIR__ . '/Fixtures/store.json'), true);
        $actual = Swagger::export($swagger->registry['/store']);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group cli
     */
    public function testCliTool()
    {
        $path = __DIR__ . '/Fixtures';
        $swagger = Swagger::discover($path);
        $output = sys_get_temp_dir();
        $pathToCli = dirname(dirname(__DIR__)) . '/bin/swagger';
        `$pathToCli $path --output $output`;
        foreach (array('user','pet','store') as $record) {
            $filename = $output . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . "$record.json";
            $this->assertJsonEqualToExpectedArray(json_decode($swagger->getResource("/{$record}"), true), file_get_contents($filename));
            unlink($filename);
        }
        rmdir($output . DIRECTORY_SEPARATOR . 'resources');
        unlink($output . DIRECTORY_SEPARATOR . 'api-docs.json');
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

    /**
     * @group Facets
     */
    public function testFacets()
    {
        $path = __DIR__ . '/Fixtures1';
        $swagger = Swagger::discover($path);
        $expected = json_decode(file_get_contents($path . '/facet.json'), true);
        $this->assertEquals($expected, Swagger::export($swagger->registry['/facet']));
    }

    /**
     * @group multi-op
     */
    public function testMultipleOperations()
    {
        $path = __DIR__ . '/Fixtures2';
        $swagger = Swagger::discover($path);
        $swagger->setDefaultApiVersion('0.2');
        $swagger->setDefaultSwaggerVersion('1.1');
        $expected = json_decode(file_get_contents($path . '/multi-op.json'), true);
        $this->assertEquals($expected, json_decode($swagger->getResource('/facet'), true));
    }

	/**
	 * @group Type-detection
	 */
	public function testRobustTypeDetection() {
		$path = __DIR__ . '/Fixtures2';
        $swagger = Swagger::discover($path);
        $expectedModel = json_decode(file_get_contents($path . '/pet.json'), true);
        $this->assertEquals($expectedModel, Swagger::export($swagger->models['Pet']));
		$expectedResource = json_decode(file_get_contents($path . '/resolve.json'), true);
        $this->assertEquals($expectedResource, Swagger::export($swagger->registry['/resolve']));
	}
}
