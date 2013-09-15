<?php

namespace SwaggerTests;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2013] [Robert Allen]
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
     * Test the examples against the json files in ExamplesOutput.
     * @group examples
     * @dataProvider getExampleDirs
     * @param string $example
     */
    public function testExample($exampleDir)
    {
        $swagger = Swagger::discover($this->examplesDir($exampleDir));
        $dir = new \DirectoryIterator($this->outputDir($exampleDir));
        foreach ($dir as $entry) {
            if ($entry->getExtension() === 'json') {
                $name = $entry->getBasename('.json');
                if (isset($swagger->registry['/'.$name])) { // Resource?
                    $this->assertOutputEqualsJson($entry->getPathname(), $swagger->getResource('/'.$name), 'Resource "/'.$name.'" doesn\'t match expected output in "'.$entry->getPathname().'"');
                } elseif ($name === 'api-docs') { // Listing?
                    $this->assertOutputEqualsJson($entry->getPathname(), $swagger->getResourceList(), 'Resource listing  doesn\'t match expected output in "'.$entry->getPathname().'"');
                } elseif (isset($swagger->models[$name])) { // Model
                    $this->assertOutputEqualsJson($entry->getPathname(), Swagger::jsonEncode($swagger->models[$name]), 'Model "'.$name.'" doesn\'t match expected output in "'.$entry->getPathname().'"');
                } else {
                    $this->fail('Resource or model "'.$name.'" not detected in "'.$this->examplesDir($exampleDir).'"');
                }
            }
        }
    }

    /**
     * Swagger before 0.8 implemented \Serializable
     * This is no longed need, which this testcase demonstrates.
     */
    public function testSerializeUnserialize()
    {
        $original = Swagger::discover($this->examplesDir('Facet'));
        $serialized = serialize($original);
        $swagger = unserialize($serialized);
        $this->assertEquals($original->models, $swagger->models);
        $this->assertEquals($original->registry, $swagger->registry);
        $this->assertOutputEqualsJson('Facet/facet.json', $swagger->getResource('/facet'));
    }

    /**
     * An example of how to cache a Swagger instance.
     * Although you'll probably just going to cache the json output, or even better generate the json files as a build step.
     * Swagger before 0.8 intergraded Caching, this is removed from \Swagger\Swagger which allows for better caching controle.
     */
    public function testCaching()
    {
        $cache = new \Doctrine\Common\Cache\ArrayCache();
        $swagger = Swagger::discover($this->examplesDir('Petstore/'));
        $cache->save('swagger', serialize($swagger));
        $swag1 = unserialize($cache->fetch('swagger'));
        $this->assertInstanceOf('Swagger\Swagger', $swag1);
        $this->assertEquals($swagger->models, $swag1->models);
        $this->assertEquals($swagger->registry['/user'], $swag1->registry['/user']);
    }

    /**
     * @group cli
     */
    public function testCliTool()
    {
        $swagger = Swagger::discover($this->examplesDir('Petstore'));
        $tmpDir = sys_get_temp_dir();
        $command = dirname(dirname(__DIR__)).'/bin/swagger';
        shell_exec(escapeshellcmd($command).' '.escapeshellarg($this->examplesDir('Petstore')).' --output '.escapeshellarg($tmpDir));
        foreach (array('user', 'pet', 'store') as $record) {
            $json = $swagger->getResource("/{$record}");
            $filename = $tmpDir.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR."$record.json";
            $this->assertOutputEqualsJson($filename, $json);
            unlink($filename);
        }
        rmdir($tmpDir.DIRECTORY_SEPARATOR.'resources');
        unlink($tmpDir.DIRECTORY_SEPARATOR.'api-docs.json');
    }
    /**
     * @group multi-op
     */
//    public function testMultipleOperations()
//    {
//        $path = __DIR__ . '/Fixtures2';
//        $swagger = Swagger::discover($path);
//        $swagger->setDefaultApiVersion('0.2');
//        $swagger->setDefaultSwaggerVersion('1.3');
//        $expected = json_decode(file_get_contents($path . '/multi-op.json'), true);
//        $this->assertEquals($expected, json_decode($swagger->getResource('/facet'), true));
//    }

    /**
     * Verify that the jsonEncode method respects the prettyPrint flag.  This test assumes that JSON is considered
     * "prettyPrint" if it consists of more than one line.
     *
     * @covers Swagger\Swagger::jsonEncode
     */
    public function testJsonEncodePrettyPrint()
    {
        $swagger = new Swagger();
        $data = array(
            'some' => 'data',
            'to be' => 'pretty printted'
        );
        $prettyJson = $swagger->jsonEncode($data, true);
        $this->assertNotCount(1, explode("\n", $prettyJson));
        $flatJson = $swagger->jsonEncode($data, false);
        $this->assertCount(1, explode("\n", $flatJson));
    }

    /**
     * dataProvider for testExample
     * @return type
     */
    public function getExampleDirs()
    {
        $examples = array();
        $dir = new \DirectoryIterator($this->outputDir());
        foreach ($dir as $entry) {
            if ($entry->isDir() && $entry->isDot() === false) {
                $examples[] = array($entry->getFilename());
            }
        }
        return $examples;
    }

    public function assertOutputEqualsJson($outputFile, $json, $message = '')
    {
        if (file_exists($this->outputDir($outputFile))) {
            $outputFile = $this->outputDir($outputFile);
        }
        $expectedArray = json_decode(file_get_contents($outputFile), true);
        $error = json_last_error();
        if ($error !== JSON_ERROR_NONE) {
            $this->fail('File: "'.$outputFile.'" doesn\'t contain valid json, error '.$error);
        }
        if (is_string($json) === false) {
            $this->fail('Not a (json) string');
        }
        $actualArray = json_decode($json, true);
        $error = json_last_error();
        if ($error !== JSON_ERROR_NONE) {
            $this->fail('invalid json, error '.$error);
        }
        $this->sort($expectedArray, '"'.$outputFile.'"');
        $this->sort($actualArray, 'generated json');
        $expectedJson = Swagger::jsonEncode($expectedArray, true);
        $actualJson = Swagger::jsonEncode($actualArray, true);
        return $this->assertEquals($expectedJson, $actualJson, $message);
    }

    /**
     * Sorts the array to improve matching and debugging the differrences.
     * Used by assertOutputEqualsJson
     * @param array $array
     * @retrun void
     */
    private function sort(&$array, $origin = 'unknown')
    {
        ksort($array);
        if (isset($array['properties'])) { // a model?
            ksort($array['properties']);
            foreach (array_keys($array['properties']) as $property) {
                ksort($array['properties'][$property]);
            }
        }
        if (isset($array['apis'])) { // a resource?
            usort($array['apis'], function ($a, $b) {
                return strcmp($a['path'], $b['path']);
            });
            foreach (array_keys($array['apis']) as $api) {
                ksort($array['apis'][$api]);
                if (isset($array['apis'][$api]['operations'])) {
                    usort($array['apis'][$api]['operations'], function ($a, $b) {
                        return strcmp($a['method'].' '.$a['nickname'], $b['method'].' '.$b['nickname']);
                    });
                    foreach (array_keys($array['apis'][$api]['operations']) as $operation) {
                        ksort($array['apis'][$api]['operations'][$operation]);
                        if (isset($array['apis'][$api]['operations'][$operation]['parameters'])) {
                            usort($array['apis'][$api]['operations'][$operation]['parameters'], function ($a, $b) {
                                $aName = array_key_exists('name', $a) ? $a['name'] : '';
                                $bName = array_key_exists('name', $b) ? $b['name'] : '';
                                return strcmp($aName, $bName);
                            });
                            foreach (array_keys($array['apis'][$api]['operations'][$operation]['parameters']) as $parameter) {
                                ksort($array['apis'][$api]['operations'][$operation]['parameters'][$parameter]);
                            }
                        }
                        if (isset($array['apis'][$api]['operations'][$operation]['responseMessages'])) {
                            usort($array['apis'][$api]['operations'][$operation]['responseMessages'], function ($a, $b) {
                                return strcmp($a['code'], $b['code']);
                            });
                            foreach (array_keys($array['apis'][$api]['operations'][$operation]['responseMessages']) as $responseMessage) {
                                ksort($array['apis'][$api]['operations'][$operation]['responseMessages'][$responseMessage]);
                            }
                        }
                    }
                } elseif (array_key_exists('models', $array) || count($array['apis'][$api]) > 2) { //  not a resource-listing?
                    $this->fail('No operations in api "'.$array['apis'][$api]['path'].'" for "'.$origin.'"');
                }
            }
        }
        if (isset($array['models'])) { // models inside a resource?
            ksort($array['models']);
            foreach (array_keys($array['models']) as $model) {
                $this->sort($array['models'][$model], $origin);
            }
        }
    }

    protected function examplesDir($example = '')
    {
        return realpath(__DIR__.'/../../Examples').'/'.$example;
    }

    protected function outputDir($example = '')
    {
        return realpath(__DIR__.'/../ExamplesOutput').'/'.$example;
    }
}