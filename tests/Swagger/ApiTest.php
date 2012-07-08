<?php

namespace SwaggerTests;
use Swagger\Api;
/**
 * Api test case.
 * @group Api
 */
class ApiTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var \Swagger\Api
     */
    private $Api;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        $this->fixture =<<<EOF
{
    "apis":{
        "/leadresponder/{leadresponder_id}":{
            "operations":[
                {
                    "tags":["MLR"],
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
                            "description":"ID of the route being requested",
                            "required":"true",
                            "allowMultiple":"false",
                            "dataType":"integer",
                            "name":"organic_id",
                            "paramType":"path"
                        },
                        {
                            "description":"organic_route being updated",
                            "required":"true",
                            "allowMultiple":"false",
                            "dataType":"organic_route",
                            "name":"organic_route",
                            "paramType":"body"
                        }
                    ],
                    "httpMethod":"PUT",
                    "path":"/{leadresponder_id}",
                    "responseClass":"organic_route",
                    "summary":"Updates the existing organic designated by the {organic_id}",
                    "responseTypeInternal":"Model_LeadResponder_Route"
                }
            ],
            "path":"/leadresponder/{leadresponder_id}"
        }
    },
    "basePath":"http://org.local/v1",
    "swaggerVersion":"1.0",
    "apiVersion":"1",
    "path":"/leadresponder",
    "value":"Gets collection of organics",
    "description":"This is a long description of what it does",
    "produces":[
        "application/json",
        "application/json+hal",
        "application/json-p",
        "application/json-p+hal",
        "application/xml",
        "application/xml",
        "application/xml+hal"
    ]
}
EOF;


    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {

        $this->Api = null;

        parent::tearDown();
    }

    /**
     *
     */
    public function testApi ()
    {
        $this->Api = new \Swagger\Api('\\Organic\\RoutesController');
        $actual = $this->Api->results;
        $this->assertEquals(json_decode($this->fixture, true), $actual);

    }

}

