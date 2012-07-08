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

        // TODO Auto-generated ModelTest::setUp()

         $this->Model = new Model('Model_Organic_Route');
        $this->fixture = <<<EOF
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
     * Tests Model->__construct()
     */
    public function test__construct ()
    {
        $actual = $this->Model->results;
        $this->assertEquals(json_decode($this->fixture, true), $actual);

    }

}

