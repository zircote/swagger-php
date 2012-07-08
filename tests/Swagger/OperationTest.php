<?php

namespace SwaggerTests;
use Swagger\Operation;

/**
 * Operation test case.
 * @group Operation
 */
class OperationTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Operation
     */
    private $Operation;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        $this->fixture = <<<EOF
{
    "tags":["MLR"],
    "errorResponses":[
        {
            "code":"403",
            "reason":"User Not Authorized"
        }
    ], "parameters":[],
    "httpMethod":"GET",
    "responseClass":"List[leadresonder_route]",
    "summary":"Fetches the leadresponder corresponding the the provided ID",
    "responseTypeInternal":"Model_LeadResponder_RouteCollection"
}
EOF;

        $reflect = new \ReflectionClass('LeadResponder_RoutesIdController');
        $rm = $reflect->getMethod('getAction');
         $this->Operation = new Operation($rm, null);

    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->Operation = null;

        parent::tearDown();
    }

    /**
     * Tests Operation->__construct()
     */
    public function test__construct ()
    {
        $this->assertEquals(json_decode($this->fixture, true), $this->Operation->results);

    }

}

