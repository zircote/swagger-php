<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;


use Swagger\StaticAnalyser;
use Swagger\Analysis;

class NestedPropertyTest extends SwaggerTestCase
{

    public function testOk()
    {
        $analyser = new StaticAnalyser();
        $analysis = new Analysis();
        $processors = Analysis::processors();

        $analysis->addAnalysis($analyser->fromFile(__DIR__ . '/Fixtures/NestedProperty.php'));
        // Post processing
        $analysis->process($processors);

        $expectedJson = <<<JSON
{
    "swagger": "2.0",
    "paths": {

    },
    "definitions": {
        "NestedProperty": {
            "properties": {
                "parentProperty": {
                    "properties": {
                        "babyProperty": {
                            "properties": {
                                "theBabyOfBaby": {
                                    "properties": {
                                        "theBabyOfBabyBaby": {
                                            "type": "string"
                                        }
                                    },
                                    "type": "boolean"
                                }
                            },
                            "type": "boolean"
                        }
                    },
                    "type": "boolean"
                }
            }
        }
    }
}
JSON;
        $this->assertEquals($expectedJson, json_encode($analysis->swagger, JSON_PRETTY_PRINT));
    }
}
