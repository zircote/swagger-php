<?php

namespace SwaggerTests;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2014] [Robert Allen]
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
        $swagger = new Swagger($this->examplesDir($exampleDir));
        $dir = new \DirectoryIterator($this->outputDir($exampleDir));
        $options = array(
            'output' => 'json'
        );
        foreach ($dir as $entry) {
            if ($entry->getExtension() === 'json') {
                $name = $entry->getBasename('.json');
                if (isset($swagger->registry['/'.$name])) { // Resource?
                    $this->assertOutputEqualsJson($entry->getPathname(), $swagger->getResource('/'.$name, $options), 'Resource "/'.$name.'" doesn\'t match expected output in "'.$entry->getPathname().'"');
                } elseif ($name === 'api-docs') { // Listing?
                    $this->assertOutputEqualsJson($entry->getPathname(), $swagger->getResourceList($options), 'Resource listing  doesn\'t match expected output in "'.$entry->getPathname().'"');
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
        $original = new Swagger($this->examplesDir('Facet'));
        $serialized = serialize($original);
        $swagger = unserialize($serialized);
        $this->assertEquals($original->models, $swagger->models);
        $this->assertEquals($original->registry, $swagger->registry);
        $this->assertOutputEqualsJson('Facet/facet.json', $swagger->getResource('/facet', array('output' => 'json')));
    }

    /**
     * Filter resource list by api version
     */
    public function testResourceFilter() {
        $swagger = new Swagger($this->examplesDir('Petstore'));
        $swagger->registry['/pet']->apiVersion = 4; // Set "/pet" to a version below 1

        $before = $swagger->getResourceList();
        $this->assertCount(3, $before['apis'], 'The /pet, /user and /store resources');

        // Filter out all unstable versions
        $swagger->registry = array_filter($swagger->registry, function ($resource) {
            return version_compare($resource->apiVersion, 4, '==');
        });
        $after = $swagger->getResourceList();
        $this->assertCount(1, $after['apis']);
        $this->assertEquals('/pet', $after['apis'][0]['path'], 'Resources /user and /store didn\'t match the filter and only /pet remains');
    }

    /**
     * @group cli
     */
    public function testCliTool()
    {
        $swagger = new Swagger($this->examplesDir('Petstore'));
        $tmpDir = sys_get_temp_dir();
        $command = dirname(dirname(__DIR__)).'/bin/swagger';
        shell_exec(escapeshellcmd($command).' '.escapeshellarg($this->examplesDir('Petstore')).' --output '.escapeshellarg($tmpDir));
        foreach (array('user', 'pet') as $record) {
            $json = $swagger->getResource('/'.$record, array('output' => 'json'));
            $filename = $tmpDir.DIRECTORY_SEPARATOR.$record.'.json';
            $this->assertOutputEqualsJson($filename, $json);
            unlink($filename);
        }
        unlink($tmpDir.DIRECTORY_SEPARATOR.'api-docs.json');
    }

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

    public function testModelInheritance()
    {
        $code = <<<END
<?php
/**
 * @SWG\Model()
 */
class Parent {
    /**
     * @var string
     * @SWG\Property(required=true)
     */
    protected \$name;
}
/**
 * @SWG\Model()
 */
class Child extends Parent {
    /**
     * @var int
     * @SWG\Property(required=true)
     */
    protected \$id;
}
END;
        $swagger = new Swagger();
        $swagger->examine($code);

        // Assert parser & parent
        $this->assertCount(2, $swagger->models);
        $this->assertCount(1, $swagger->models['Parent']->properties);
        $this->assertEquals('name', $swagger->models['Parent']->properties[0]->name);
        $this->assertContains('name', $swagger->models['Parent']->required);
        $this->assertEquals(array('name'), $swagger->models['Parent']->required);
        // Assert child and inheritance
        $this->assertCount(2, $swagger->models['Child']->properties);
        $this->assertEquals('id', $swagger->models['Child']->properties[0]->name);
        $this->assertEquals('name', $swagger->models['Child']->properties[1]->name);
        $this->assertContains('id', $swagger->models['Child']->required);
        $this->assertEquals(array('id', 'name'), $swagger->models['Child']->required);
    }

    function testPropertyInheritance() {
        $code = <<<END
<?php
/**
 * Class UserBase
 *
 * @SWG\Model(id="UserBase")
 */
class UserBase {
    /**
     * @SWG\Property()
     */
    public \$email;
}

/**
 * @SWG\Model(id="UserNew",required="['email']")
 */
class UserNew extends UserBase { }

/**
 * @SWG\Model(id="UserUpdate")
 */
class UserUpdate extends UserBase { }
END;
        $swagger = new Swagger();
        $swagger->examine($code);
        $this->assertCount(1, $swagger->models['UserNew']->required);
        $this->assertNull($swagger->models['UserUpdate']->required);

    }
    /**
     * dataProvider for testExample
     * @return array
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
        $expected = json_decode(file_get_contents($outputFile));
        $error = json_last_error();
        if ($error !== JSON_ERROR_NONE) {
            $this->fail('File: "'.$outputFile.'" doesn\'t contain valid json, error '.$error);
        }
        if (is_string($json) === false) {
            $this->fail('Not a (json) string');
        }
        $actual = json_decode($json);
        $error = json_last_error();
        if ($error !== JSON_ERROR_NONE) {
            $this->fail('invalid json, error '.$error);
        }
        $expectedJson = Swagger::jsonEncode($this->sort($expected, '"'.$outputFile.'" '), true);
        $actualJson = Swagger::jsonEncode($this->sort($actual, 'generated json '), true);
        return $this->assertEquals($expectedJson, $actualJson, $message);
    }

    /**
     * Sorts the array to improve matching and debugging the differrences.
     * Used by assertOutputEqualsJson
     * @param object $object
     * @return The sorted object
     */
    private function sort(\stdClass $object, $origin = 'unknown')
    {
        static $sortMap = null;
        if ($sortMap === null) {
            $sortMap = array(
                // property -> algorithm
                'apis' => function ($a, $b) { return strcasecmp($a->path, $b->path); },
                'operations' => function ($a, $b) { return strcasecmp($a->nickname, $b->nickname); },
                'parameters' => function ($a, $b) { return strcasecmp($a->name, $b->name); },
                'responseMessages' => function ($a, $b) { return strcasecmp($a->code, $b->code); },
                'produces' => 'strcasecmp',
                'consumes' => 'strcasecmp',
                'required' => 'strcasecmp',
                'enum' => 'strcasecmp',
                'scopes' => function ($a, $b) { return strcasecmp($a->scope, $b->scope); },
                'oauth2' => function ($a, $b) { return strcasecmp($a->scope, $b->scope); },
            );
        }
        $data = get_object_vars($object);
        ksort($data);
        foreach ($data as $property => $value) {
            if (is_object($value)) {
                $data[$property] = $this->sort($value, $origin .'->'.$property);
            }
            if (is_array($value)) {
                $sort = @$sortMap[$property];
                if ($sort) {
                    usort($value, $sort);
                    $data[$property] = $value;
                } else {
                    // echo 'no sort for '.$origin.'->'.$property."\n";die;
                }
                foreach ($value as $i => $element) {
                    if (is_object($element)) {
                        $data[$property][$i] = $this->sort($element, $origin.'->'.$property.'['.$i.']');
                    }
                }
            }
        }
        return (object)$data;
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