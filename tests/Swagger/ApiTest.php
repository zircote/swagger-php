<?php

require_once 'Swagger\Api.php';

require_once 'PHPUnit\Framework\TestCase.php';

/**
 * Swagger_Api test case.
 */
class Swagger_ApiTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Swagger_Api
     */
    private $Swagger_Api;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        
        // TODO Auto-generated Swagger_ApiTest::setUp()
        
        $this->Swagger_Api = new Swagger_Api(/* parameters */);
    
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        // TODO Auto-generated Swagger_ApiTest::tearDown()
        
        $this->Swagger_Api = null;
        
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
     * Tests Swagger_Api->__construct()
     */
    public function test__construct ()
    {
        // TODO Auto-generated Swagger_ApiTest->test__construct()
        $this->markTestIncomplete("__construct test not implemented");
        
        $this->Swagger_Api->__construct(/* parameters */);
    
    }

}

