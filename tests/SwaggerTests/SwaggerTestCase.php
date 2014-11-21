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

abstract class SwaggerTestCase extends \PHPUnit_Framework_TestCase
{

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

    /**
     *
     * @param string $php
     * @return Swagger
     */
    protected function examineCode($php) {
        $swagger = new Swagger();
        $swagger->examine("<?php\n".$php);
        return $swagger;
    }
}
