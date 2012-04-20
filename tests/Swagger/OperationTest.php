<?php

require_once 'Swagger\Operation.php';

require_once 'PHPUnit\Framework\TestCase.php';

/**
 * Swagger_Operation test case.
 */
class Swagger_OperationTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Swagger_Operation
     */
    private $Swagger_Operation;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        
        // TODO Auto-generated Swagger_OperationTest::setUp()
        
        $this->Swagger_Operation = new Swagger_Operation(/* parameters */);
    
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        // TODO Auto-generated Swagger_OperationTest::tearDown()
        
        $this->Swagger_Operation = null;
        
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
     * Tests Swagger_Operation->__construct()
     */
    public function test__construct ()
    {
        // TODO Auto-generated Swagger_OperationTest->test__construct()
        $this->markTestIncomplete("__construct test not implemented");
        
        $this->Swagger_Operation->__construct(/* parameters */);
    
    }

}

