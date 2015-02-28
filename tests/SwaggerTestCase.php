<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use PHPUnit_Framework_TestCase;
use stdClass;
use Swagger\Annotations\AbstractAnnotation;
use Swagger\Annotations\Swagger;
use Swagger\Context;
use Swagger\Parser;

class SwaggerTestCase extends PHPUnit_Framework_TestCase {

    /**
     *
     * @param string $comment Contents of a comment block
     * @return AbstractAnnotation[]
     */
    protected function parseComment($comment) {
        $parser = new Parser();
        $caller = Context::detect(1);
        $context = Context::detect(2);
        $context->line = -2;
        $context->filename = $caller->filename . ':' . $caller->line;
        return $parser->parseContents("<?php\n/**\n * " . implode("\n * ", explode("\n", $comment)) . "\n*/", $context);
    }

    /**
     *
     * @param string $expectedFile File containing the excepted json.
     * @param Swagger $actualSwagger
     * @param string $message
     */
    public function assertSwaggerEqualsFile($expectedFile, $actualSwagger, $message = '') {
        $expected = json_decode(file_get_contents($expectedFile));
        $error = json_last_error();
        if ($error !== JSON_ERROR_NONE) {
            $this->fail('File: "' . $expectedFile . '" doesn\'t contain valid json, error ' . $error);
        }
        $json = json_encode($actualSwagger);
        if ($json === false) {
            $this->fail('Failed to encode swagger object');
        }
        $actual = json_decode($json);
        $expectedJson = json_encode($this->sorted($expected, $expectedFile), JSON_PRETTY_PRINT);
        $actualJson = json_encode($this->sorted($actual, 'Swagger'), JSON_PRETTY_PRINT);
        return $this->assertEquals($expectedJson, $actualJson, $message);
    }

    /**
     * Sorts the object to improve matching and debugging the differences.
     * Used by assertSwaggerEqualsFile
     * @param object $object
     * @return stdClass The sorted object
     */
    protected function sorted(stdClass $object, $origin = 'unknown') {
        static $sortMap = null;
        if ($sortMap === null) {
            $sortMap = [
                // property -> algorithm
                'parameters' => function ($a, $b) {
                    return strcasecmp($a->name, $b->name);
                },
                'responses' => function ($a, $b) {
                    return strcasecmp($a->name, $b->name);
                },
                'headers' => function ($a, $b) {
                    return strcasecmp($a->header, $b->header);
                },
                'allOf' => function ($a, $b) {
                    return strcasecmp(implode(',',array_keys(get_object_vars($a))), implode(',',array_keys(get_object_vars($b))));
                }
            ];
        }
        $data = get_object_vars($object);
        ksort($data);
        foreach ($data as $property => $value) {
            if (is_object($value)) {
                $data[$property] = $this->sorted($value, $origin . '->' . $property);
            }
            if (is_array($value)) {
                if (count($value) > 1) {
                    if (gettype($value[0]) === 'string') {
                        $sortFn = 'strcasecmp';
                    } else {
                        $sortFn = @$sortMap[$property];
                    }
                    if ($sortFn) {
                        usort($value, $sortFn);
                        $data[$property] = $value;
                    } else {
                        echo 'no sort for '.$origin.'->'.$property."\n";die;
                    }
                }
                foreach ($value as $i => $element) {
                    if (is_object($element)) {
                        $data[$property][$i] = $this->sorted($element, $origin . '->' . $property . '[' . $i . ']');
                    }
                }
            }
        }
        return (object) $data;
    }
}
