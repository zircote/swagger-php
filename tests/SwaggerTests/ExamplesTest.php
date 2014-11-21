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

class ExamplesTest extends SwaggerTestCase
{
    /**
     * Test the examples against the json files in ExamplesOutput.
     *
     * @group examples
     * @dataProvider getExampleDirs
     * @param string $exampleDir
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
                if (isset($swagger->registry['/' . $name])) { // Resource?
                    $this->assertOutputEqualsJson($entry->getPathname(), $swagger->getResource('/' . $name, $options), 'Resource "/' . $name . '" doesn\'t match expected output in "' . $entry->getPathname() . '"');
                } elseif ($name === 'api-docs') { // Listing?
                    $this->assertOutputEqualsJson($entry->getPathname(), $swagger->getResourceList($options), 'Resource listing  doesn\'t match expected output in "' . $entry->getPathname() . '"');
                } elseif (isset($swagger->models[$name])) { // Model
                    $this->assertOutputEqualsJson($entry->getPathname(), Swagger::jsonEncode($swagger->models[$name]), 'Model "' . $name . '" doesn\'t match expected output in "' . $entry->getPathname() . '"');
                } else {
                    $this->fail('Resource or model "' . $name . '" not detected in "' . $this->examplesDir($exampleDir) . '"');
                }
            }
        }
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

}
