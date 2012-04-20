<?php

require_once 'Swagger\Param.php';

require_once 'PHPUnit\Framework\TestCase.php';

/**
 * Swagger_Param test case.
 */
class Swagger_ParamTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Swagger_Param
     */
    private $Swagger_Param;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        
        // TODO Auto-generated Swagger_ParamTest::setUp()
        
        $this->Swagger_Param = new Swagger_Param(/* parameters */);
    
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        // TODO Auto-generated Swagger_ParamTest::tearDown()
        
        $this->Swagger_Param = null;
        
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
     * Tests Swagger_Param->__construct()
     */
    public function test__construct ()
    {
        // TODO Auto-generated Swagger_ParamTest->test__construct()
        $this->markTestIncomplete("__construct test not implemented");
        
        $this->Swagger_Param->__construct(/* parameters */);
    
    }

}

