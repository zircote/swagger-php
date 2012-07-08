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
        $this->fixture = <<<'EOF'
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
EOF;


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
     *
     */
    public function testModels ()
    {
         $this->Models = new Models(
             array('Model_Organic_Route','Model_LeadResponder_RouteCollection')
         );
        $this->assertEquals(json_decode($this->fixture, true), $this->Models->results);

    }

}

