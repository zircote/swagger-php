<?php

namespace SwaggerTests;
use Swagger\Api;
/**
 * Api test case.
 */
class ApiTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Api
     */
    private $Api;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();

        // TODO Auto-generated ApiTest::setUp()

//         $this->Api = new Api(/* parameters */);

    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        // TODO Auto-generated ApiTest::tearDown()

        $this->Api = null;

        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct ()
    {
        // TODO Auto-generated constructor
    }

    /**
     * Tests Api->__construct()
     */
    public function test__construct ()
    {
        // TODO Auto-generated ApiTest->test__construct()
        $this->markTestIncomplete("__construct test not implemented");

        $this->Api->__construct(/* parameters */);

    }

}

