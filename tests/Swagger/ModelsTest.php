<?php

namespace SwaggerTests;
use Swagger\Models;

/**
 * Models test case.
 * @group Models
 */
class ModelsTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Models
     */
    private $Models;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        $this->fixture = <<<EOF
{
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
            "tag":{
                "type":"string",
                "description":""
            },
            "arrayItem":{
                "type":"array",
                "description":""
            },
            "refArr":{
                "type":"array",
                "description":""
            },
            "enumVal":{
                "type":"array",
                "description":""
            },
            "integerParam":{
                "description":"This is an integer Param",
                "type":"integer"
            }
        }
    }
}

EOF;

         $this->Models = new Models(
             array('Model_Organic_Route','Model_LeadResponder_RouteCollection')
         );

    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->Models = null;

        parent::tearDown();
    }

    /**
     * Tests Models->__construct()
     */
    public function test__construct ()
    {
        $this->assertEquals(json_decode($this->fixture, true), $this->Models->results);

    }

}

