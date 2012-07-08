<?php

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
 * @category   Swagger
 * @package    Swagger
 * @subpackage UnitTests
 */
namespace SwaggerTests;
use Swagger\Swagger;

/*
 *
 *
 * @category   Swagger
 * @package    Swagger
 * @subpackage UnitTests
 */
class ResourceTest extends \PHPUnit_Framework_TestCase
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
        $this->_resourceFixture = array(
            'apis' => array(
                array(
                    'path'        => '/leadresponder',
                    'description' => 'Gets collection of leadresponders'
                )
            ),
            'basePath'       => 'http://org.local/v1',
            'swaggerVersion' => '1.0',
            'apiVersion'     => 1
        );
        $api = <<<'JSON'
{
    "apis":[
        {
            "operations":[
                {
                    "tags":[
                        "MLR"
                    ],
                    "errorResponses":[
                        {
                            "code":"403",
                            "reason":"User Not Authorized"
                        }
                    ],
                    "parameters":[

                    ],
                    "httpMethod":"GET",
                    "responseClass":"List[leadresonder_route]",
                    "responseTypeInternal":"Model_LeadResponder_RouteCollection",
                    "summary":"Fetches the leadresponder corresponding the the provided ID"
                },
                {
                    "tags":[
                        "MLR"
                    ],
                    "errorResponses":[
                        {
                            "code":"403",
                            "reason":"User Not Authorized"
                        }
                    ],
                    "parameters":[
                        {
                            "description":"leadresponder_route being created",
                            "required":"true",
                            "allowMultiple":"false",
                            "dataType":"leadresponder_route",
                            "name":"leadresponder_route",
                            "paramType":"body"
                        }
                    ],
                    "httpMethod":"POST",
                    "responseClass":"leadresonder_route",
                    "responseTypeInternal":"Model_LeadResponder_Route",
                    "summary":"Creates a new leadresponder"
                }
            ],
            "path":"/leadresponder"
        },
        {
            "operations":[
                {
                    "tags":[
                        "MLR"
                    ],
                    "errorResponses":[
                        {
                            "code":"400",
                            "reason":"Invalid ID Provided"
                        },
                        {
                            "code":"403",
                            "reason":"User Not Authorized"
                        },
                        {
                            "code":"404",
                            "reason":"Lead Responder Not Found"
                        }
                    ],
                    "parameters":[
                        {
                            "description":"ID of the leadresponder being requested",
                            "required":"true",
                            "allowMultiple":"false",
                            "dataType":"integer",
                            "name":"leadresponder_id",
                            "paramType":"path"
                        },
                        {
                            "description":"leadresponder_route being updated",
                            "required":"true",
                            "allowMultiple":"false",
                            "dataType":"leadresponder_route",
                            "name":"leadresponder_route",
                            "paramType":"body"
                        }
                    ],
                    "httpMethod":"PUT",
                    "path":"/{leadresponder_id}",
                    "responseTypeInternal":"Model_LeadResponder_Route",
                    "responseClass":"leadresonder_route",
                    "summary":"Updates the existing leadresponder designated by the {leadresponder_id}"
                }
            ],
            "path":"/leadresponder/{leadresponder_id}"
        }
    ],
    "basePath":"http://org.local/v1",
    "swaggerVersion":"1.0",
    "apiVersion":"1",
    "path":"/leadresponder",
    "value":"Gets collection of leadresponders",
    "description":"This is a long description of what it does",
    "produces":[
        "application/json",
        "application/json+hal",
        "application/json-p",
        "application/json-p+hal",
        "application/xml",
        "application/xml",
        "application/xml+hal"
    ],
    "models":{
        "leadresonder_route":{
            "id":"leadresonder_route",
            "description":"some long description of the model",
            "properties":{
                "usr_mlr_route_id":{
                    "type":"integer",
                    "description":"some long winded description."
                },
                "route":{
                    "type":"string",
                    "description":"some long description of the model."
                },
                "createdDate":{
                    "type":"string",
                    "description":""
                },
                "tags":{
                    "type":"array",
                    "description":"this is a reference to `tag`",
                    "items" : {
                        "$ref": "tag"
                    }
                },
                "arrayItem":{
                    "type":"array",
                    "description":"This is an array of strings",
                    "items" : {
                        "type": "string"
                    }
                },
                "refArr":{
                    "type":"array",
                    "description":"This is an array of integers.",
                    "items" : {
                        "type": "integer"
                    }
                },
                "enumVal":{
                    "type":"string",
                    "description":"This is an enum value.",
                    "enum": ["Two Pigs","Duck","And 1 Cow"]
                },
                "integerParam":{
                    "description":"This is an integer Param",
                    "type":"integer"
                }
            }
        }
    }
}

JSON;
        $this->_apiFixture      = json_decode($api, true);
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
        $path = realpath(dirname(__DIR__) . '/fixtures');
        $swagger = Swagger::discover($path);

        $resource = $swagger->getResource('http://org.local/v1', true);
//        echo $resource, PHP_EOL, PHP_EOL;
        $api = $swagger->getApi('http://org.local/v1', '/leadresponder');
//        echo $api, PHP_EOL, PHP_EOL;
        $this->assertEquals(
            $this->_apiFixture, json_decode((string)$api, true)
        );
        $this->assertEquals(
            $this->_resourceFixture, json_decode((string)$resource, true)
        );
    }

}
