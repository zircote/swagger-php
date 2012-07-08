<?php

namespace SwaggerTests;
use Swagger\Model;

/**
 * Model test case.
 * @group Model
 */
class ModelTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Model
     */
    private $Model;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();

        $this->fixture = <<<'EOF'
{
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
            "enum": ["Two Pigs","One Duck","And a Cow"]
        },
        "integerParam":{
            "description":"This is an integer Param",
            "type":"integer"
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

        $this->Model = null;

        parent::tearDown();
    }

    /**
     *
     */
    public function testModel ()
    {
        $this->Model = new Model('Model_Organic_Route');
        $actual = $this->Model->results;
//        print_r($actual);
        $this->assertEquals(json_decode($this->fixture, true), $actual);

    }

}

